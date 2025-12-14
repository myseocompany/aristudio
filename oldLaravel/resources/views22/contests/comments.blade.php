@extends('layout')

@section('content')


<form method="POST" action="/contests/comments/show">
        {{ csrf_field() }}

          <div class="form-group">
            <label for="name">Post Code</label>
            <input type="text" class="form-control" id="post_id" name="post_id" placeholder="Post Id" required="required">
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
            <label for="date">Fanpage ID</label>
            <input type="texxt" class="form-control" id="fanpage" name="fanpage" placeholder="Fan Page" required="required">
          </div>
          <div>
            <a href="https://findmyfbid.com/">Obtener el fanpage ID</a>
          </div>

          <div class="form-group">
            <label for="title">Token</label>
            <input type="text" class="form-control" id="token" name="token" placeholder="Token" required="required">
          </div>
          <div>
             <a href="https://developers.facebook.com/tools/explorer/" target="_blank">graph explorer facebook - SocialCRM - Pagina</a><br>
             <a href="https://www.facebook.com/dialog/pagetab?%20app_id=242399522626953%20&redirect_uri=https%3A%2F%2Fmyseo.com.co%2Fcontests%2Fcomments">URL instalacion</a>
          </div>
          <button type="submit" class="btn brn-sum btn-primary my-2 my-sm-0">Submit</button>
        </form>



@endsection