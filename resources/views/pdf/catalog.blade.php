<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title> Product Catalogue</title>
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600&display=swap" rel="stylesheet">
		<!-- <link rel="stylesheet" href="{{public_path('css/style.css')}}"> -->
		<style>
			@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap');

			* {
				margin: 0;
				padding: 0;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				-ms-box-sizing: border-box;
				box-sizing: border-box;
			}

			ul {
				list-style: none;
			}

			p {
				font-family: 'Josefin Sans', sans-serif;
				font-style: normal;
				letter-spacing: 0.1px;
				text-rendering: optimizeLegibility;
				font-size: 14px;
				letter-spacing: 0.3px;
				line-height: 25px;
				color: #555555;
				font-weight: 400;
			}

			body {
				font-family: 'Josefin Sans', sans-serif;
				background-color: #f2eee3
			}

			a,
			a:active,
			a:focus {
				text-decoration: none;
				outline: none;
			}

			a:hover {
				text-decoration: none;
			}

			button:focus {
				outline: none;
			}

			textarea:focus,
			input:focus {
				outline: none;
			}

			h1,
			h2,
			h3,
			h4,
			h5,
			h6 {
				font-family: 'Josefin Sans', sans-serif;
				color: #333
			}

			ul {
				margin: 0px;
				padding: 0px;
				list-style: none;
			}

			/*header css start*/

			header {
				width: 100%;
				float: left;
				padding: 40px 0px
			}

			.container {
				/* max-width: 900px; */
				padding-right: 5px;
				padding-left: 5px;
				margin-right: auto;
				margin-left: auto;
			}

			.col-6-left {
				width: 50%;
				float: left;
			}
			.col-6-right {
				width: 50%;
				float: right;
			}

			header h1 {
				font-weight: 500;
				font-size: 23px;
				letter-spacing: 1px;
			}

			header p {
				padding-top: 20px;
				font-size: 17px;
			}

			header img {
				float: right
			}

			.home-section {
				width: 100%;
				float: left
			}

			.left-home-section img {}

			.home-section {
				width: 100%;
				float: left;
				padding: 30px 0px
			}

			.left-home-section {
				width: 19%;
				float: left;
				height: 500px;
			}

			.right-home-section {
				width: 81%;
				float: right;
				overflow: hidden;
				height: 500px;
			}

			.right-home-section img {
				width: 100%;
				float: left;
			}

			.home-section ul {}

			.home-section ul li {
				height: 33.33%;
				font-size: 13px;
				padding: 70px 0px 0px 0px;
			}

			.home-section ul li:nth-of-type(1) {
				text-align: right;
			}

			.home-section ul li:nth-of-type(2) {
				text-align: center;
			}

			.home-section ul li:nth-of-type(3) {
				text-align: left;
			}

			.home-section ul li {
				/* Safari */
				-webkit-transform: rotate(-90deg);

				/* Firefox */
				-moz-transform: rotate(-90deg);

				/* IE */
				-ms-transform: rotate(-90deg);

				/* Opera */
				-o-transform: rotate(-90deg);
			}

			.listing-section {
				width: 100%;
				float: left;
				padding: 80px 0px
			}

			.listing-section h3 {
				border-bottom: 2px solid #c5c5c5;
				padding-bottom: 6px;
				letter-spacing: 1px;
				font-weight: 600;
			}

			.listing-section h3 img {
				float: right;
				width: 120px;
				margin-top: -9px;
			}

			.col-12 {
				width: 100%;
				float: left;
				border-bottom: 2px solid #c5c5c5;
			}

			.one {
				width: 30%;
				float: left;
				padding: 15px 0px
			}

			.two {
				width: 70%;
				float: left;
				padding: 15px 0px 15px 20px
			}

			.one img {
				width: 100%;
				border-radius: 25px;
			}

			.listing-ul {
				width: 100%;
				float: left
			}

			.listing-ul li {
				width: 100%;
				float: left;
				padding: 6px 0px;
			}

			.listing-ul li div:nth-of-type(1) {
				width: 25%;
				float: left
			}

			.listing-ul li div:nth-of-type(2) {
				width: 55%;
				float: left
			}

			.listing-ul li div:nth-of-type(3) {
				width: 20%;
				float: left
			}

			.listing-ul li h5 {
				font-size: 12px;
				border-bottom: 1px solid #a0a0a0;
				padding-bottom: 7px;
				margin-bottom: 10px;
			}

			.listing-ul li h6 {
				font-size: 12px;
				padding: 5px 0px;
				font-weight: 600;
			}

			.listing-ul li span {
				font-size: 13px;
				font-weight: 400;
				color: #5d5b5b;
			}
			.clear {
				clear: both;
			}
		</style>
	</head>
	<body>
		<!--header section start-->
		<header>
			<div class="container">
					<div class="col-6-left">
						<h1>PRODUCT CATALOGUE</h1>
						<p>Status: In progress <br />
							Products available: 01.01.2021 - 03.03.2021
						</p>
					</div>
					<div class="col-6-right">
						<img src="{{public_path('images/logo.png')}}">
					</div>
			</div>
		</header>
		<div class="clear"></div>
		<!--header section end-->
		<div class="home-section">
			<div class="container">
				<div class="left-home-section">
					<ul>
						<li><span> Conatct name: Resirqel <br />Email: resirqel@resirqel.no<br />Phone: (+47) 900 00 900</span> </li>
						<li><span>CUSTOMER NAME </span> </li>
						<li><span>PROJECT NAME <br />PROJECT ADRESS</span> </li>
					</ul>

				</div>

				<div class="right-home-section">
					<img src="{{public_path('images/bannerimg.jpg')}}">
				</div>
			</div>
		</div>
	</body>
</html>