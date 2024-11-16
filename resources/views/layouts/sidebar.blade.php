<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li >
                    <a href="#"><i class="la la-dashboard"></i> <span> {{__('translate.Dashboard')}}</span></a>
                    {{-- <ul style="display: none;">
                        <li><a class="active" href="index.html">Admin Dashboard</a></li>
                        <li><a href="employee-dashboard.html">Employee Dashboard</a></li>
                    </ul> --}}
                </li>
                <li class="menu-title">
                    <span>{{__('translate.User Managment')}}</span>
                </li>
                @role('admin')
                <li class="submenu">
                    <a href="#"><i class="la la-user"></i> <span> {{__('translate.User Managment')}}</span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a href="{{route('users.index',['role'=>'user'])}}">{{__('translate.Users')}}</a></li>
                        <li class="submenu">
                            <a href="#"><span> {{__('translate.Admins')}}</span> <span class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li><a href="{{route('users.index',['role'=>'admin'])}}">{{__('translate.View All')}}</a></li>
                                <li><a href="{{route('users.create')}}">{{__('translate.Add New')}}</a></li>
                            </ul>
                        </li>                        <li class="submenu">
                            <a href="#"><span> {{__('translate.Companies')}}</span> <span class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li><a href="{{route('users.index',['role'=>'company'])}}">{{__('translate.View All')}}</a></li>
                                <li><a href="{{route('users.create')}}">{{__('translate.Add New')}}</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><span> {{__('translate.Stores')}}</span> <span class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li><a href="{{route('users.index',['role'=>'store'])}}">{{__('translate.View All')}}</a></li>
                                <li><a href="{{route('users.create')}}">{{__('translate.Add New')}}</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endrole
                @can('add drivers')
                <li >
                    <a href="{{route('drivers.index')}}"><i class="la la-taxi"></i> <span>{{__('translate.Driver')}}</span></a>

                </li>
                @endcan
                @role('admin')
                <li >
                    <a href="{{route('roles.index')}}"><i class="la la-ticket"></i> <span>{{__('translate.Role & Permission')}}</span></a>

                </li>
                <li class="menu-title">
                    <span>{{__('translate.orders')}}</span>
                </li>
                <li >
                    <a href="{{route('categories.index')}}"><i class="la la-object-ungroup"></i> <span>{{__('translate.Category')}}</span></a>

                </li>
                <li >
                    <a href="{{route('products.index')}}"><i class="las la-tshirt"></i> <span>{{__('translate.Product')}}</span></a>

                </li>

                @endrole
                @can('view my orders')
                <li >
                    <a href="{{route('categories.index')}}"><i class="la la-bullhorn"></i> <span>{{__('translate.orders')}}</span></a>

                </li>
                @endcan
                <li >
                    <a href="{{route('categories.index')}}"><i class="la la-chart-bar"></i> <span>{{__('translate.Report')}}</span></a>

                </li>
                @role('admin')
                <li class="menu-title">
                    <span>{{__('translate.Setting')}}</span>
                </li>
                <li >
                    <a href="{{route('categories.index')}}"><i class="la la-tasks"></i> <span>{{__('translate.Quiz')}}</span></a>

                </li>
                <li >
                    <a href="{{route('categories.index')}}"><i class="la la-city"></i> <span>{{__('translate.Area')}}</span></a>

                </li>
                <li >
                    <a href="{{route('categories.index')}}"><i class="la la-puzzle-piece"></i> <span>{{__('translate.Promo code')}}</span></a>

                </li>
                <li >
                    <a href="{{route('categories.index')}}"><i class="la la-gift"></i> <span>{{__('translate.Gift')}}</span></a>

                </li>
                <li >
                    <a href="{{route('categories.index')}}"><i class="la la-info"></i> <span>{{__('translate.Info')}}</span></a>

                </li>
                <li >
                    <a href="{{route('categories.index')}}"><i class="la la-cog"></i> <span>{{__('translate.Setting')}}</span></a>

                </li>
                @endrole

            </ul>
        </div>
    </div>
</div>
