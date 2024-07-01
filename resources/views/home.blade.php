@extends('layout.app')
@section('content')
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Widget</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Widgets</li>
							</ol>
						</nav>
					</div>
					
				</div>
				<!--end breadcrumb-->
				
				
				
				<!--end row-->
				<h6 class="mb-0 text-uppercase">Color Static Widgets</h6>
				<hr/>
				<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
					<div class="col">
						<div class="card radius-10 bg-primary bg-gradient">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0 text-white">Total Orders</p>
										<h4 class="my-1 text-white">845</h4>
									</div>
									<div class="text-white ms-auto font-35"><i class='bx bx-cart-alt'></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 bg-danger bg-gradient">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0 text-white">Total Income</p>
										<h4 class="my-1 text-white">$89,245</h4>
									</div>
									<div class="text-white ms-auto font-35"><i class='bx bx-dollar'></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 bg-warning bg-gradient">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0 text-dark">Total Users</p>
										<h4 class="text-dark my-1">24.5K</h4>
									</div>
									<div class="text-dark ms-auto font-35"><i class='bx bx-user-pin'></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 bg-success bg-gradient">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0 text-white">Comments</p>
										<h4 class="my-1 text-white">8569</h4>
									</div>
									<div class="text-white ms-auto font-35"><i class='bx bx-comment-detail'></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 bg-success">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0 text-white">Revenue</p>
										<h4 class="my-1 text-white">$4805</h4>
										<p class="mb-0 font-13 text-white"><i class="bx bxs-up-arrow align-middle"></i>$34 from last week</p>
									</div>
									<div class="widgets-icons bg-white text-success ms-auto"><i class="bx bxs-wallet"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 bg-info">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0 text-dark">Total Customers</p>
										<h4 class="my-1 text-dark">8.4K</h4>
										<p class="mb-0 font-13 text-dark"><i class="bx bxs-up-arrow align-middle"></i>$24 from last week</p>
									</div>
									<div class="widgets-icons bg-white text-dark ms-auto"><i class="bx bxs-group"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 bg-danger">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0 text-white">Store Visitors</p>
										<h4 class="my-1 text-white">59K</h4>
										<p class="mb-0 font-13 text-white"><i class="bx bxs-down-arrow align-middle"></i>$34 from last week</p>
									</div>
									<div class="widgets-icons bg-white text-danger ms-auto"><i class="bx bxs-binoculars"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 bg-warning">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0 text-dark">Bounce Rate</p>
										<h4 class="my-1 text-dark">34.46%</h4>
										<p class="mb-0 font-13 text-dark"><i class="bx bxs-down-arrow align-middle"></i>12.2% from last week</p>
									</div>
									<div class="widgets-icons bg-white text-dark ms-auto"><i class='bx bx-line-chart-down'></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--end row-->
			</div>
		</div>
		<!--end page wrapper -->

	@endsection