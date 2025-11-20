<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">
    <!-- DataTables Bootstrap 5 Styling -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <!-- Select2 CSS -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css" />


    <style>
        div.dt-container.dt-empty-footer tbody>tr:last-child>* {
            border-bottom: 1px solid rgba(201, 193, 193, 0.3);
        }

        table.dataTable>thead>tr>th,
        table.dataTable>thead>tr>td {
            padding: 10px;
            padding-right: 10px;
            border-bottom: 1px solid rgba(201, 193, 193, 0.3);
        }

        .dt-paging {
            float: right;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px !important;
        }

        .error {
            color: red;
        }

        .alert ul {
            margin: 0 !important;
        }

        .hgi {
            font-size: 18px !important;
        }

        .sidebar-nav ul .sidebar-item .first-level .sidebar-item>.sidebar-link {
            font-size: 14px;
        }
    </style>

</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!--  App Topstrip -->

        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="./index.html" class="text-nowrap logo-img"
                        style="font-size: 25px;font-weight: bold;color: black;">
                        <i class="hgi hgi-stroke hgi-reddit"></i> Website Logo
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-6"></i>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">

                            @if (Auth::user()->user_type == 'admin')
                                <a class="sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                                    <i class="hgi hgi-stroke hgi-dashboard-square-02"></i>
                                    <span class="hide-menu">{{ __('messages.dashboard') }}</span>
                                </a>
                            @endif
                        </li>

                        @if (Auth::user()->user_type == 'admin')
                            <li class="sidebar-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <a class="sidebar-link justify-content-between has-arrow {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                                    href="javascript:void(0)"
                                    aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="d-flex">
                                            <i class="hgi hgi-stroke hgi-baby-boy-dress"></i>
                                        </span>
                                        <span class="hide-menu">Products</span>
                                    </div>
                                </a>

                                <ul
                                    class="collapse first-level {{ request()->routeIs('admin.products.*') ? 'show' : '' }}">

                                    <li class="sidebar-item">
                                        <a class="sidebar-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}"
                                            href="{{ route('products.index') }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-circle"></i>
                                                </div>
                                                <span class="hide-menu">All Products</span>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="sidebar-item">
                                        <a class="sidebar-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}"
                                            href="{{ route('products.create') }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-circle"></i>
                                                </div>
                                                <span class="hide-menu">Add Product</span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>



                            <li class="sidebar-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <a class="sidebar-link justify-content-between has-arrow {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                                    href="javascript:void(0)"
                                    aria-expanded="{{ request()->routeIs('admin.categories.*') ? 'true' : 'false' }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="d-flex">
                                            <i class="hgi hgi-stroke hgi-baby-boy-dress"></i>
                                        </span>
                                        <span class="hide-menu">Categories</span>
                                    </div>
                                </a>

                                <ul
                                    class="collapse first-level {{ request()->routeIs('admin.categories.*') ? 'show' : '' }}">

                                    <li class="sidebar-item">
                                        <a class="sidebar-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}"
                                            href="{{ route('categories.index') }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-circle"></i>
                                                </div>
                                                <span class="hide-menu">All Categories</span>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="sidebar-item">
                                        <a class="sidebar-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}"
                                            href="{{ route('categories.create') }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-circle"></i>
                                                </div>
                                                <span class="hide-menu">Add Category</span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        <li>
                            <span class="sidebar-divider lg"></span>
                        </li>

                    </ul>
                    <div class="unlimited-access hide-menu bg-light-secondary position-relative mb-7 mt-5 rounded">
                        <div class="d-flex">
                            <div class="unlimited-access-title me-3">
                                <h6 class="fw-semibold fs-4 mb-6 text-dark w-85">Logout
                                    {{ ucwords(Auth::user()->name) }}</h6>
                                <a href="{{ route('logout') }}" class="btn btn-secondary fs-2 fw-semibold">Logout</a>
                            </div>
                            <div class="unlimited-access-img">
                                <img src="../assets/images/backgrounds/rocket.png" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-bell"></i>
                                <div class="notification bg-primary rounded-circle"></div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                                <div class="message-body">
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        Item 1
                                    </a>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        Item 2
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

                            <li class="nav-item dropdown">
                                <a class="nav-link " href="javascript:void(0)" id="drop2"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt=""
                                        width="35" height="35" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="javascript:void(0)"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="{{ route('logout') }}"
                                            class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            <div class="body-wrapper-inner">
                <div class="container-fluid">

                    @yield('content')
                    {{--
          <div class="py-6 px-6 text-center">
            <p class="mb-0 fs-4">&copy; {{ date('Y') }} VoxCodes.</p>
          </div> --}}
                </div>
            </div>
        </div>
    </div>


    <footer class="mt-auto py-3 bg-light text-center">
        &copy; 2025 VoxCodes.
    </footer>

    <script></script>



    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables Core JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <!-- DataTables Responsive JS -->
    <script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>
    <!-- DataTables Bootstrap 5 Integration JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @yield('scripts')
</body>

</html>
