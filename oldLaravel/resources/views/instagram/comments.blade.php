@extends('layout')

@section('content')



<?php 

define('FACEBOOK_APPID','242399522626953');
define('FACEBOOK_SECRET','70bf87e26953f3caf1bfad7f4eea8db9');


 ?>
<a href="{{$authentication_url}}"> Click here to Authenticate</a>

@if(isset($error) && ($error!=""))
<div class="alert alert-danger" role="alert">
  {!!$error!!}
</div>
@endif

<form method="POST" action="/contests/instagram/show">
        {{ csrf_field() }}
          
          <div class="form-group">
            <label for="name">Post URL</label>
            <input type="text" class="form-control" id="post_url" name="post_url" placeholder="Post URL" required="required" onblur="getMediaID()">
          </div>

          <div class="form-group">
            <label for="name">Media Id</label>
            <input type="text" class="form-control" id="media_id" name="media_id" placeholder="Media ID" required="required">
          </div>

          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Title" required="required">
          </div>
          
          <div class="form-group">
            <label for="date">Date</label>
            <input type="datetime-local" class="form-control" id="finish_date" name="finish_date" placeholder="Finish Date" required="required" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
          </div>

          <div class="form-group">
            <label for="date">Unique</label>
            <input type="checkbox" class="form-control" id="unique" name="unique" placeholder="Finish Date"  value="true">
          </div>

          
          <div class="form-group">
            <label for="title">Token</label>
            <input type="text" class="form-control" id="token" name="token" placeholder="Token" required="required" @if(isset($access_token) && ($access_token!="")) value="{{$access_token}}" @endif >
          </div>
          <div>
             <a href="https://instagram.pixelunion.net/" target="_blank">Get acces token</a>
          </div>

          
          <button type="submit" class="btn brn-sum btn-primary my-2 my-sm-0">Submit</button>
        </form>

<script>
  function getMediaID(){
    Url = $("#post_url").val();
    $.ajax({
        type: 'GET',
        url: 'http://api.instagram.com/oembed?callback=&url='+Url, //You must define 'Url' for yourself
        cache: false,
        dataType: 'json',
        jsonp: false,
        success: function (data) {
          var MediaID = data.media_id;
          console.log(MediaID);
          $('#media_id').val(MediaID);
       }
    });
  }
</script>


@endsection