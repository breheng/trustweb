<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\place;
use App\building;
use App\reservation;
use App\ActivityType;
use App\Activity;
use App\CommonConfig;
use \DateTime;
use \DateTimeZone;
use \DateInterval;
use App\common\HDReportHandler;

class UserController extends Controller
{
  private $bh;

    function __construct(){
      $this->bh = new BookingHelper;
    }

    // view info
    public function getCustInfo(Request $req){
      $input = app('request')->all();

  		$rules = [
  			'staff_id' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      $staffdata = User::where('id', $req->staff_id)->first();

      // return $staffdata;
      if($staffdata){

      } else {
        return $this->respond_json(404, 'cust not found', []);
      }

      $staff = $this->getStaffInfo($staffdata);

      return $this->respond_json(200, 'OK', $staff);
    }

    public function ListAllowedBuilding(Request $req){
      $input = app('request')->all();

  		$rules = [
  			'staff_id' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      $staff = User::where('id', $req->staff_id)->first();
      $ret = array();

      if(isset($staff->allowed_building)){
        $allowedbuilding = json_decode($staff->allowed_building);
        foreach ($allowedbuilding as $buildid) {
          array_push($ret, $this->bh->getBuildingStat($buildid));
        }
      }
      $errcode = 200;
      if($ret){
        $errcode = 300;
      }

      return $this->respond_json($errcode, 'Allowed buildings', $ret);

    }


    // reserve seat
    public function ReserveSeat(Request $req){
      $input = app('request')->all();

  		$rules = [
  			'staff_id' => ['required'],
        'seat_id' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      // find this staff
      $thisstaff = User::where('id', $req->staff_id)->first();
      if($thisstaff){
        // release old reservation if any
        $this->bh->clearReservation($thisstaff);
      } else {
        return $this->respond_json(404, 'staff 404', $input);
      }

      $checkseat = $this->bh->checkSeat($req->seat_id);

      if($checkseat['code'] != 200){
        return $checkseat;
      }

      $time = new DateTime('NOW');
      $time->setTimezone(new DateTimeZone('+0800'));
      $minutes_to_add = env('TRUST_RESERVE_DUR', 15);
      $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

      // proceed to reserve the seat
      $seat = place::where('id', $req->seat_id)->first();
      $seat->status = 2;
      $seat->reserve_staff_id = $req->staff_id;
      $seat->reserve_expire = $time;
      $seat->save();

      // create the reservation record
      $reserve = new reservation;
      $reserve->place_id = $req->seat_id;
      $reserve->user_id = $req->staff_id;
      $reserve->expire_time = $time;
      $reserve->status = 1;
      $reserve->save();

      // update back to the user
      $thisstaff->curr_reserve = $reserve->id;
      $thisstaff->save();

      // return $this->respond_json(200, 'seat reserved', $reserve);
      return $this->respond_json(200, 'seat reserved', $this->bh->getReserveInfo($reserve->id));

    }

    // cancel reservation
    function ReserveCancel(Request $req){
      $input = app('request')->all();

  		$rules = [
  			'staff_id' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      $staff = User::where('id', $req->staff_id)->first();
      $this->bh->clearReservation($staff);

      return $this->respond_json(200, 'reservation cleared', []);

    }

    // check in from reservation
    function CheckinFromReserve(Request $req){
      $input = app('request')->all();

  		$rules = [
  			'staff_id' => ['required'],
        'qr_code' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      $theuser = User::where('id', $req->staff_id)->first();
      if(isset($theuser->curr_reserve)){
        // check reservation status
        $theresv = reservation::where('id', $theuser->curr_reserve)->first();
        if($theresv->status == 0){
          return $this->respond_json(401, 'Reservation no longer active and maybe overwritten by others', $theresv);
        }

        // check the seat this qr belongs to
        $seatbyqr = place::where('qr_code', $req->qr_code)->first();
        if($seatbyqr){
          // check if it's the same seat reserved earlier
          if($seatbyqr->id != $theresv->place_id){
            return $this->respond_json(401, 'Not the reserved seat');
          }
        } else {
          return $this->respond_json(404, 'QR code not registered');
        }

        // proceed with checkin
        $cekin = $this->bh->checkIn($theuser, $theresv->place_id);
        return $this->respond_json(200, 'Checkin successful', $cekin);
      } else {
        // no active reservation
        return $this->respond_json(401, 'No active reservation', $this->getStaffInfo($theuser));
      }
    }

    // direct check in
    function CheckinDirect(Request $req){
      $input = app('request')->all();

  		$rules = [
  			'staff_id' => ['required'],
        'seat_id' => ['required']
  		];

  		$validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      // just in case, check the status
      $seatstatys = $this->bh->checkSeat($req->seat_id);

      if($seatstatys['code'] != 200){
        return $seatstatys;
      }

      $lat = 0;
      $long = 0;

      if($req->filled('lat')){
        $lat = $req->lat;
      }

      if($req->filled('long')){
        $long = $req->long;
      }

      // // UNCOMMENT THIS TO RE-ENABLE GEOLOCATION VALIDATION
      $cconfig = CommonConfig::where('key', 'geo_checkin')->first();
      if($cconfig && $cconfig->value == 'true'){
        if($this->bh->inCorrectPlace($req->seat_id, $lat, $long) == false){
          return $this->respond_json(403, 'Not in correct location', $input);
        }
      }


      $theuser = User::where('id', $req->staff_id)->first();
      $cekin = $this->bh->checkIn($theuser, $req->seat_id, $lat, $long);
      return $this->respond_json(200, 'Checkin successful', $cekin);
    }

    // check out
    function CheckOut(Request $req){
      $input = app('request')->all();

  		$rules = [
  			'staff_id' => ['required']
  		];

      $validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      $this->bh->checkOut($req->staff_id);

      return $this->respond_json(200, 'Checkout successful');

    }

    function Find(Request $req){
      $input = app('request')->all();

  		$rules = [
  			'input' => ['required']
  		];

      $validator = app('validator')->make($input, $rules);
  		if($validator->fails()){
  			return $this->respond_json(412, 'Invalid input', $input);
  		}

      $finder = new HDReportHandler;
      $data = $finder->findStaff($req->input);
      $errc = 200;
      if($data->count() == 0){
        $errc = 404;
      }

      return $this->respond_json($errc, 'Search result for ' . $req->input, $data);

    }




    // internal functions ======================

    public function updateStaffProfile(Request $req){

    }

    private function getStaffInfo($staffdata){

      $staff = [
        'status' => $staffdata->status,
        'name' => $staffdata->name,
        'email' => $staffdata->email,
        'mobile_no' => $staffdata->mobile_no,
        'photo_url' => $staffdata->photo_url,
        'staff_id' => $staffdata->staff_id,
        'role' => $staffdata->role,
        'allowed_building' => json_decode($staffdata->allowed_building)
      ];

      if(isset($staffdata->curr_reserve)){
        $staff['curr_reserve'] = $this->bh->getReserveInfo($staffdata->curr_reserve);
      }

      if(isset($staffdata->curr_checkin)){
        $staff['curr_checkin'] = $this->bh->getCheckinInfo($staffdata->curr_checkin);
      }

      if(isset($staffdata->last_checkin)){
        $staff['last_checkin'] = $this->bh->getCheckinInfo($staffdata->last_checkin);
      }

      return $staff;
    }

    public function getActivityList($task_id){
      $actlist = Activity::where('task_id', $task_id)
        ->orderBy('date', 'ASC')
        ->get();

      // append the readable activity type
      foreach ($actlist as $aact) {
        $acttype = ActivityType::find($aact->act_type);
        $aact['act_type_desc'] = $acttype->descr;
      }

      return $actlist;
    }
}
