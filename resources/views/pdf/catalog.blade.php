
 <!--header section start-->
 @if($pdfData)
  @foreach($pdfData as $eachData)
 <header>
	<div class="container">
		<div class="col-6">
			<h1>PRODUCT CATALOGUE</h1>
			<p>Status: In progress <br/>
			 Products available: 01.01.2021 - 03.03.2021
			</p>
		</div>
		
		<div class="col-6">
			<img src="{{ URL::asset('images/logo.png') }}">
		</div>
		
	</div>
 </header>
  <!--header section end-->
  
  <div class="home-section">
	<div class="container">
		<div class="left-home-section">
			<ul>
				<li><span> Conatct name: {{$eachData['project_mang_name']}} <br/>Email: {{$eachData['project_mang_email']}}<br/>Phone: {{$eachData['project_mang_mobile']}}</span> </li>
				<li><span>{{$eachData['customer']['customer_name']}}</span> </li>
				<li><span>{{$eachData['project_name']}} <br/>{{$eachData['project_address']}} </span> </li>
			</ul>
		 
		</div>
		
		<div class="right-home-section">
			<img height="900" src="https://resources-products.s3.us-east-2.amazonaws.com/uploads/projects/{{$eachData['project_image']}}">
		</div>
	</div>
  </div>
  <div style="page-break-after:always">&nbsp;</div> 
	<div class="listing-section">
		<div class="container">
			<h3>PRODUKTKATALOG <img src="{{ URL::asset('images/logo.png') }}"></h3>
			@foreach($eachData['products'] as $eachprod)
				<div class="col-12">
					<div class="one">
						<img height="417" src="https://resources-products.s3.us-east-2.amazonaws.com/uploads/products/{{$eachprod['product_image']}}">
					</div>
					<div class="two">
						<ul class="listing-ul">
							<li>
								<div><h5> 01 BÆRENDE </h5></div>
								<div><h5> BYGNINGSDEL: Branntrapp </h5></div>
								<div><h5> PRODUKT No.: 1 </h5></div>
							</li>
							
							<li>
								<div>
									<h6>PLASSERING I BYGG </h6>
									<span> Mot tak</span>
								</div>
								<div>
									<h6>BESKRIVELSE </h6>
									<span> Enkel galvanisert branntrapp mellom to etasjer. Modulær</span>
								</div>
							</li>
							
							<li>
								<div>
									<h6>MENGDE  </h6>
									<span>1 STK</span>
								</div>
								<div>
									<h6>PRODUKTINFO </h6>
									<span>nil</span>
								</div>
							</li>
							
							<li>
								<div>
									<h6>DIMENSJONER  </h6>
									<span>L: 10 MM<br/>B: 10 MM <br/>H: 10 MM</span>
								</div>
								<div>
									<h6>POTENSIALE </h6>
									<span>Godt</span>
								</div>
							</li>
							
							
							<li>
								<div>
									<h6>PROD. ÅR  </h6>
									<span>Usikkert</span>
								</div>
								<div>
									<h6>VURDERING </h6>
									<span>I seg selv enkel å ombruke. Trenger ikke CE- merking</span>
								</div>
							</li>
							
							
							<li>
								<div>
									<h6>PRODUSENT </h6>
									<span>nil</span>
								</div>
								<div>
									<h6>FORUTSETNING </h6>
									<span>Kan enkelt demonteres, men må ev. hentes ut ved riving av tak- er innebygget</span>
								</div>
							</li>
							
							<li>
								<div>
									<h6>DOKUMENTASJON </h6>
									<span>nil</span>
								</div>
								<div>
									<h6>ANBEFALING </h6>
									<span>Demontering ved riveentreprenør. Ombruk ved Resirqel</span>
								</div>
							</li>

						</ul>
						
					</div>
					
				</div>
			@endforeach
		</div>
	</div>
	<div style="page-break-after:always">&nbsp;</div> 
  @endforeach
 @endif 
<!-- 	
 </body>

</html> -->