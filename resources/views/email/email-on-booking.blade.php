<html>
<head>
  <meta charset="utf-8">
  <title>Spize (River Valley)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style type="text/css">
    body {
      margin: 0;
    }

    .page-reservation {
      max-width: 1250px;
      color: #363E3F;
      background-color: #f8f8f8;
      margin: 0 auto;
      height: calc(100vh - 170px);
      -vendor-animation-duration: 3s;
      -vendor-animation-delay: 2s;
      -vendor-animation-iteration-count: infinite;
      padding-top: 20px;
    }

    @media screen and (max-height: 480px) {
      .page-reservation {
        height: 400px;
      }
    }

    .page-reservation h2 {
      margin: 0;
      font-size: 25px;
      color: #363E3F;
      padding: 15px 0;
      text-align: center;
    }

    .page-reservation .confirm .icon {
      text-align: center;
    }

    .page-reservation .confirm .icon i {
      font-size: 69px;
      color: #008F52;
    }

    .page-reservation .confirm h2 {
      padding: 0 20px;
      line-height: 1.5;
    }

    .page-reservation .confirm p {
      text-align: center;
      padding: 0 15%;
      line-height: 1.5;
    }

    .page-reservation .confirm hr {
      width: 65%;
    }

    .inf {
      text-align: center;
      margin-bottom: 25px;
    }

    .page-hearder {
      padding: 0;
      width: 100%;
      margin: 0;
      display: block;
      position: relative;
      background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1) 10%, rgba(0, 0, 0, 0.8) 100%);
      -vendor-animation-duration: 3s;
      -vendor-animation-delay: 2s;
      -vendor-animation-iteration-count: infinite;
    }

    .page-hearder.animated {
      animation-fill-mode: initial;
    }

    .page-hearder .parallax-container {
      background: url({{ $outlet->outlet_cover_image }}) no-repeat;
      background-size: cover;
    }

    .page-hearder .parallax-container .parallax {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: -1;
    }

    .page-hearder .parallax-container .parallax img {
      position: absolute;
      left: 50%;
      bottom: 0;
      width: 100%;
      min-width: 100%;
      min-height: 100%;
      -webkit-transform: translate3d(0, 0, 0);
      transform: translate3d(0, 0, 0);
      -webkit-transform: translateX(-50%);
      transform: translateX(-50%);
    }

    .page-hearder .info {
      font-size: 14px;
      position: absolute;
      bottom: -15px;
      color: #a09f9c;
      width: 70%;
      padding: 30px 0 0 0;
    }

    .page-hearder .info p {
      margin-left: 15px;
    }

    .page-hearder .info .outlet-name {
      color: #fff;
      background: #383838;
      padding: 5px 0 5px 10px;
    }

    .page-hearder .info .outlet-name h1 {
      font-size: 20px;
      margin: 0;
      padding: 0;
    }

    .confirm {
      text-align: center;
    }

    .confirm a {
      background-color: #008F52;
      color: #fff;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
    }

    .review hr {
      margin-top: 20px;
      width: 65%;
    }

    .review a {
      background-color: rgba(0, 143, 82, 0.91);
      color: #fff;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      display: inline-block;
      width: 60px;
    }

  </style>
</head>
<body>
<div class="page-hearder">
  <div class="parallax-container">
    <div class="info" style="transform: translateY(-100%)">
      <p>
        {{ $outlet->outlet_address }}
      </p>
      <div class="outlet-name">
        <h1>{{ $outlet->outlet_name }}</h1>
      </div>
    </div>
    </div>
  </div>
</div>
<div>
  <div class="page-reservation">
    <div class=" animated fadeInRight">
      <h2>Reservation {{ $reservation->confirm_id }}</h2>
      <div class="confirm">
        <div>
          <div class="icon"><span>
            <img  src="{{ url('images/ic_check_circle_green_48px.png') }}"></span>
          </div>
          <h2>Your Reservation Details</h2>
          <hr>
        </div>
      </div>
      <div class="inf">
        <p><b>Time:</b> {{ $reservation->date->format('dS M Y') }}, {{ $reservation->date->format('h:i a') }}</p>
        <p><b>Outlet:</b> {{ $outlet->outlet_name }}</p>
        <p><b>Pax:</b>
          <span>{{ $reservation->adult_pax }} Adults </span>
          <span>{{ $reservation->children_pax }} Children </span>
        </p>
        <p><b>Email:</b> {{ $reservation->email }}</p>
      </div>
      <div class="confirm">
        <a href="{{ $reservation->edit_url }}">Edit/Cancel</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>