<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<select name="outlet-location">
    <option selected="selected" disabled="disabled">Location</option>
    {{--loop through outlet to print out here--}}
</select>
@if(isset($data))
	{{$data}}
@endif

</body>
</html>