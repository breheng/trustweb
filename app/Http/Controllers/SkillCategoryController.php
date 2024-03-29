<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SkillCategory;
use App\CommonSkillset;

class SkillCategoryController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
      $this->middleware('AdminGate');
  }

  // skil categories

  public function list(Request $req){

    $cfgs = SkillCategory::all();

    if($req->filled('alert')){
      return view('admin.skillcat', [
        'alert' => $req->alert,
        'data' => $cfgs
      ]);
    } else {
      return view('admin.skillcat', ['data' => $cfgs]);
    }
  }

  public function addedit(Request $req){
    $cfg = SkillCategory::where('name', $req->name)->first();
    $msg = 'record updated';
    if(!$cfg){
      $cfg = new SkillCategory;
      $cfg->name = $req->name;
      $cfg->added_by = \Session::get('staffdata')['id'];
      $msg = 'record added';
    }
    $cfg->sequence = $req->seqorder;
    $cfg->save();

    return redirect(route('sc.list', ['alert' => 'record updated']));
  }

  public function edit(Request $req){
    $cfg = SkillCategory::findOrFail($req->id);
    $cfg->name = $req->name;
    $cfg->sequence = $req->seqorder;
    $cfg->save();

    return redirect(route('sc.list', ['alert' => 'record updated']));
  }

  public function del(Request $req){
    $delskill = SkillCategory::find($req->id);
    if($delskill){
      // dont delete misc skill cat
      if(strcasecmp($delskill->name, 'misc') == 0){
        return redirect(route('sc.list', ['alert' => 'Must not delete misc skill category']));
      } else {
        if($delskill->CommonSkillset->count() > 0){
          // find a misc category
          $misc = SkillCategory::where('name', 'Misc')->first();
          if(!$misc){
            // create one if not exist
            $misc = new SkillCategory;
            $misc->name = 'Misc';
            $misc->sequence = 99;
            $misc->added_by = $req->session()->get('staffdata')['id'];
            $misc->save();
          }

          // reassign to misc skill cat
          $delskill->CommonSkillset->update(['skill_category_id' => $misc->id]);

        }

        // then only delete the skill cat
        $delskill->delete();
      }
    }
    return redirect(route('sc.list', ['alert' => 'record deleted']));
  }

  // ================================
  // tumpang shared skillset kat sini lol
  // ================================


  public function sslist(Request $req){

    $type = 'p';
    if($req->filled('cat')){
      $type = $req->cat;
    }

    if(strcasecmp($type, 'm') == 0){
      $cfgs = CommonSkillset::where('category', 'm')->get();
    } else {
      $cfgs = CommonSkillset::where('category', 'p')->get();
    }

    $skillcats = SkillCategory::all();

    if($req->filled('alert')){
      return view('admin.sharedskillset', [
        'alert' => $req->alert,
        'data' => $cfgs,
        'skillcats' => $skillcats,
        'cat' => $type
      ]);
    } else {
      return view('admin.sharedskillset', ['data' => $cfgs,
      'skillcats' => $skillcats,
      'cat' => $type]);
    }
  }

  public function ssaddedit(Request $req){
    $cfg = CommonSkillset::where('name', $req->name)->first();
    $msg = 'record updated';
    if(!$cfg){
      $cfg = new CommonSkillset;
      $cfg->name = $req->name;
      $cfg->added_by = \Session::get('staffdata')['id'];
      $cfg->category = 'p';

      // default to blank first
      $cfg->skillgroup = '';
      $cfg->skilltype = '';

      $msg = 'record added';

    }
    $cfg->skill_category_id = $req->skill_cat;
    $cfg->save();

    return redirect(route('ss.list', ['alert' => 'record updated']));
  }

  public function ssedit(Request $req){
    $cfg = CommonSkillset::findOrFail($req->id);
    $cfg->name = $req->name;
    $cfg->skill_category_id = $req->skill_cat;
    $cfg->category = $req->cat;
    $cfg->save();

    return redirect(route('ss.list', ['alert' => 'record updated']));
  }

  public function ssdel(Request $req){

    $cs = CommonSkillset::findOrFail($req->id);

    // delete all the personal skills tied to this
    $pss = $cs->PersonalSkillset;
    foreach ($pss as $key => $value) {
      $value->delete();
    }

    // then delete itself
    $cs->delete();

    return redirect(route('ss.list', ['alert' => 'record deleted']));
  }

}
