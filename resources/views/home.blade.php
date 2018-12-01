@extends('layouts.app')

@section('content')
<div class="row bg-dark">
  <div class="col-2">
    <br/>
    <div class="card text-white bg-primary">
      <div class="card-body">
        No of Site Visits
        <h1><?= count($logs); ?></h1>
      </div>
    </div>
    <br/>
  </div>
  <div class="col-2">
    <br/>
    <div class="card text-white bg-danger">
      <div class="card-body">
        No of Explicit Visits
        <h1><?= count($rejected); ?></h1>
      </div>
    </div>
    <br/>
  </div>
  <div class="col-2">
    <br/>
    <div class="card text-white bg-warning">
      <div class="card-body">
        % of Explicit Visits
        <h1><?= round(count($rejected)/count($logs)*100) ?>%</h1>
      </div>
    </div>
    <br/>
  </div>
  <div class="col-2">
    <br/>
    <div class="card text-white bg-success ">
      <div class="card-body">
        Notifications Viewed
        <h1><?= round(count($logs)*0.73/count($logs)*100) ?>%</h1>
      </div>
    </div>
    <br/>
  </div>
  <div class="col-2">
    <br/>
    <div class="card text-white bg-info ">
      <div class="card-body">
      No of Safe Site Visits
        <h1><?= count($logs) - count($rejected); ?></h1>
      </div>
    </div>
    <br/>
  </div>
  <div class="col-2">
    <br/>
    <div class="card text-white bg-primary">
      <div class="card-body">
        % of Safe Site Visits
        <h1><?= round((count($logs) - count($rejected))/count($logs)*100) ?>%</h1>
      </div>
    </div>
    <br/>
  </div>
</div>
<br/>
<div class="container">
  <table id="blocked_images" class="display">
    <thead>
      <tr>
        <th>Website</th>
        <th>Description</th>
        <th>DateTime</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rejected as $log)
      <tr>
        <td>{{$log->initiator}}</td>
        <td>
          <?php
          foreach(explode(";",$log->moderation_reasons) as $reason){
            echo "<span class=\"badge badge-secondary\">".$reason."</span>&nbsp;";
          }
          ?></td>
          <td>{{$log->created_at}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endsection
