@extends('layout_charge_account')

@section('content')
<img src="/images/logo_my_seo_company.png" height="40">
<h2>Logins</h2>
<table>
  <thead>
  </thead>
  <tbody>
    @foreach($logins as $item)
    <tr>
      <th>Url <br>Name <br> User <br> Password</th>
      

      <td>
         <a @if( substr($item->url,0,8)== 'https://') href="{{$item->url}}" @else href="https://{{$item->url}}" @endif target="_blank">{{$item->url}}<a><br>
        {{$item->name}}<br>
        {{$item->user}}<br>
        <div id="password_{{$item->id}}">
        {{$item->password}}
        </div>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<style type="text/css">
  .header{
    text-align: center;
  }

  th, td {
    padding: 0px;
    border: 1px solid;
}

  .table-body{
    padding-left: 15%; 
  }

  .container {
    margin-left: 0px;
}
</style>
@endsection



