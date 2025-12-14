@extends('layout')

@section('content')

<h1> Consulta de competencia para palabras claves </h1>

{{$keyword}}

<script>
function initQuery(response){
	console.log(response);
	console.log(response.searchInformation.totalResults);
	var total=response.searchInformation.totalResults;
	updateKeyword({{$kId}},total);
	}	
	
function updateKeyword(kId,totalResults){
	//$.get("/keywordsFinder/queryUpdate/updateValue",{keywordId:kId, total:totalResults},ajaxSuccess(data));
	$.get("/keywordsFinder/queryUpdate/updateValue",{keywordId:kId, total:totalResults},function(data){
		console.log("ajax was successed");
		console.log(data);
		location.reload();
		});
	}
	
/*function ajaxSuccess(data){
		console.log("ajax was successed");
		console.log(data);
		}*/
	
</script>



@endsection
@section("footerjs")
<script src="https://www.googleapis.com/customsearch/v1?q={{$keyword}}&cx=006667151083533909385%3Auug8opexqgc&key={{$APIkey}}&callback=initQuery"></script>
@endsection
