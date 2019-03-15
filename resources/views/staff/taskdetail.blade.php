@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Task Details</div>
                <div class="card-body">
                  <form method="POST" action="{{ route('staff.closetask', [], false) }}">
                    @csrf
                    <h5 class="card-title">Task Information</h5>
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Task Name</label>
                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" name="name" value="{{ $taskinfo['name'] }}" disabled readonly />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="remark" class="col-md-4 col-form-label text-md-right">Remark</label>
                        <div class="col-md-6">
                          <textarea rows="5" class="form-control" id="remark" name="remark"
                          disabled readonly>{{ $taskinfo['remark'] }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                      <label for="ttype" class="col-md-4 col-form-label text-md-right">Task Type</label>
                      <div class="col-md-6">
                        <input id="ttype" type="text" name="ttype" value="{{ $tasktype }}" disabled readonly />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="thours" class="col-md-4 col-form-label text-md-right">Total Hours</label>
                      <div class="col-md-6">
                        <input id="thours" type="text" name="thours" value="{{ $taskinfo['total_hours'] }}" disabled readonly />
                      </div>
                    </div>
                    <div class="form-group row">
                        <label for="addedby" class="col-md-4 col-form-label text-md-right">Added By</label>
                        <div class="col-md-6">
                            <input id="addedby" type="text" name="addedby" value="{{ $taskinfo['created_by'] }}" disabled readonly />
                        </div>
                    </div>
                    <input id="task_id" type="hidden" name="task_id" value="{{ $taskinfo['id'] }}" >
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                          @if($taskinfo['status'] == 1)
                            <a href="{{ route('staff.addact', ['task_id' => $taskinfo['id']], false) }}"><button type="button" class="btn btn-secondary">Add Activity</button></a>
                            <button type="submit" class="btn btn-primary">Close Task</button>
                          @else
                            <button type="submit" class="btn btn-primary" disabled>Closed</button>
                          @endif
                        </div>
                    </div>
                  </form>
                </div>
                <div class="card-body">
                  <h5 class="card-title">List of Activities</h5>
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Remark</th>
                        <th scope="col">Hours</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($activities as $acts)
                      <tr>
                        <td>{{ $acts['date'] }}</td>
                        <td>{{ $acts['remark'] }}</td>
                        <td>{{ $acts['hours_spent'] }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection