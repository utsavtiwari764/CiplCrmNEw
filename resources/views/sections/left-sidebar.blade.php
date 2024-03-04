<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <!-- <img src="{{ $global->logo_url }}"
             alt="AdminLTE Logo"
             class="brand-image img-fluid"> -->
             <img src="{{ asset('logo.png') }}"
             alt="AdminLTE Logo"
             class="brand-image img-fluid">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" id="sidebarnav" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <i class="nav-icon icon-speedometer"></i>
                        <p>
                            @lang('menu.dashboard')
                        </p>
                    </a>
                </li>

                @if(in_array("view_category", $userPermissions))
                <li class="nav-item">
                    <a href="{{ route('admin.job-categories.index') }}" class="nav-link {{ request()->is('admin/job-categories*') ? 'active' : '' }}">
                        <i class="nav-icon icon-grid"></i>
                        <p>
                            @lang('menu.jobCategories')
                        </p>
                    </a>
                </li>
                @endif

                @if(in_array("view_skills", $userPermissions))
                <li class="nav-item">
                    <a href="{{ route('admin.skills.index') }}" class="nav-link {{ request()->is('admin/skills*') ? 'active' : '' }}">
                        <i class="nav-icon icon-grid"></i>
                        <p>
                            @lang('menu.skills')
                        </p>
                    </a>
                </li>
                @endif
                <!-- @if(in_array("view_questions", $userPermissions))
                <li class="nav-item">
                    <a href="{{ route('admin.question') }}" class="nav-link {{ request()->is('admin/question*') ? 'active' : '' }}">
                        <i class="nav-icon icon-grid"></i>
                        <p>
                            @lang('menu.questions')
                        </p>
                    </a>
                </li>
                @endif -->

                @if(in_array("view_company", $userPermissions))
                <li class="nav-item">
                    <a href="{{ route('admin.company.index') }}" class="nav-link {{ request()->is('admin/company*') ? 'active' : '' }}">
                        <i class="nav-icon icon-film"></i>
                        <p>
                            @lang('menu.companies')
                        </p>
                    </a>
                </li>
                @endif

                @if(in_array("view_locations", $userPermissions))
                <li class="nav-item">
                    <a href="{{ route('admin.locations.index') }}" class="nav-link {{ request()->is('admin/locations*') ? 'active' : '' }}">
                        <i class="nav-icon icon-location-pin"></i>
                        <p>
                            @lang('menu.locations')
                        </p>
                    </a>
                </li>
                @endif

                @if(in_array("view_jobs", $userPermissions))
                <li class="nav-item">
                    <a href="{{ route('admin.jobs.index') }}" class="nav-link {{ request()->is('admin/jobs*') ? 'active' : '' }}">
                        <i class="nav-icon icon-badge"></i>
                        <p>
                            @lang('menu.jobs')
                        </p>
                    </a>
                </li>
                @endif

                @if(in_array("view_job_applications", $userPermissions))
                <li class="nav-item">
                    <a href="{{ route('admin.job-applications.index') }}" class="nav-link {{ request()->is('admin/job-applications*') ? 'active' : '' }}">
                        <i class="nav-icon icon-user"></i>
                        <p>
                            @lang('menu.jobApplications')
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.applications-archive.index') }}" class="nav-link {{ request()->is('admin/applications-archive*') ? 'active' : '' }}">
                        <i class="nav-icon icon-drawer"></i>
                        <p>
                            @lang('menu.candidateDatabase')
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.job-onboard.index') }}" class="nav-link {{ request()->is('admin/job-onboard*') ? 'active' : '' }}">
                        <i class="nav-icon icon-user"></i>
                        <p>
                            @lang('menu.jobOnboard')
                        </p>
                    </a>
                </li>
                @endif

                @if(in_array("view_schedule", $userPermissions))
                <li class="nav-item">
                    <a href="{{ route('admin.interview-schedule.index') }}" class="nav-link {{ request()->is('admin/interview-schedule*') ? 'active' : '' }}">
                        <i class="nav-icon icon-calendar"></i>
                        <p>
                            @lang('menu.interviewSchedule')
                        </p>
                    </a>
                </li>
                @endif

                @if(in_array("view_team", $userPermissions))
                <li class="nav-item">
                    <a href="{{ route('admin.team.index') }}" class="nav-link {{ request()->is('admin/team*') ? 'active' : '' }}">
                        <i class="nav-icon icon-people"></i>
                        <p>
                            @lang('menu.team')
                        </p>
                    </a>
                </li>
                @endif

                @if ($user->roles->count() > 0)
                    <li class="nav-item">
                        <a href="{{ route('admin.todo-items.index') }}" class="nav-link {{ request()->is('admin/todo-items*') ? 'active' : '' }}">
                            <i class="nav-icon icon-notebook"></i>
                            <p>
                                @lang('menu.todoList')
                            </p>
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                <a href="{{ route('admin.report.index') }}" class="nav-link">
                <i class="fa fa-bar-chart" aria-hidden="true"></i>
                        <p>
                            @lang('app.reports') 
                        </p>
                    </a>
                </li>
                @if(in_array("view_schedule", $userPermissions))
                @if(isset($zoom_setting->enable_zoom) && $zoom_setting->enable_zoom == 1)
                <li class="nav-item has-treeview">
                    <a href="{{ route('admin.zoom-meeting.table-view') }}" class="nav-link">
                        <i class="fa fa-video-camera"></i>
                        <p> @lang('menu.zoomMeeting')</p>
                    </a>
                </li>
                @endif
                @endif
                <li class="nav-item has-treeview @if(\Request()->is('admin/exam/*') || \Request()->is('admin/exam/course'))active menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="nav-icon icon-settings"></i>
                        <p>
                            @lang('menu.exam')
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if(in_array("manage_exam", $userPermissions))
                        @if(in_array("view_course", $userPermissions))
                        <li class="nav-item">
                            <a href="{{ route('admin.exams.course') }}" class="nav-link {{ request()->is('admin/exams/course') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.courses')</p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('admin.application-setting.index') }}" class="nav-link {{ request()->is('admin/settings/application-setting') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.subjects')</p>
                            </a>
                        </li>
                       
                       
                        @endif
                    </ul>
                </li>
                <li class="nav-item has-treeview @if(\Request()->is('admin/settings/*') || \Request()->is('admin/profile'))active menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="nav-icon icon-settings"></i>
                        <p>
                            @lang('menu.settings')
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.profile.index') }}" class="nav-link {{ request()->is('admin/profile*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p> @lang('menu.myProfile')</p>
                            </a>
                        </li>
                        @if(in_array("manage_settings", $userPermissions))
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->is('admin/settings/settings') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.businessSettings')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.application-setting.index') }}" class="nav-link {{ request()->is('admin/settings/application-setting') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.applicationFormSettings')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.currency-settings.index')}}" class="nav-link {{ request()->is('admin/settings/currency-settings') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.currencySetting')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.role-permission.index') }}" class="nav-link {{ request()->is('admin/settings/role-permission') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.rolesPermission')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.language-settings.index') }}" class="nav-link {{ request()->is('admin/settings/language-settings') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('app.language') @lang('menu.settings')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.footer-settings.index') }}" class="nav-link {{ request()->is('admin/settings/footer-settings') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.footerSettings')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.theme-settings.index') }}" class="nav-link {{ request()->is('admin/settings/theme-settings') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.themeSettings')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.smtp-settings.index') }}" class="nav-link {{ request()->is('admin/settings/smtp-settings') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.mailSetting')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.sms-settings.index') }}" class="nav-link {{ request()->is('admin/settings/sms-settings') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.smsSettings')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.storage-settings.index') }}" class="nav-link">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.storageSetting')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.linkedin-settings.index') }}" class="nav-link">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.linkedInSettings')</p>
                            </a>
                        </li>
                      <!--   @if($global->system_update == 1)
                        <li class="nav-item">
                            <a href="{{ route('admin.update-application.index') }}" class="nav-link {{ request()->is('admin/settings/update-application') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('menu.updateApplication')</p>
                            </a>
                        </li>
                        @endif -->
                            <li class="nav-item">
                                <a href="{{ route('admin.zoom-setting.index') }}" class="nav-link">
                                    <i class="fa fa-circle-o nav-icon"></i>

                                    <p> @lang('menu.zoomSetting')</p>
                                </a>
                            </li>
                      

                        @endif


                    </ul>
                </li>

                <li class="nav-header">MISCELLANEOUS</li>
                <li class="nav-item">
                    <a href="{{ url('/') }}" target="_blank" class="nav-link">
                        <i class="nav-icon fa fa-external-link"></i>
                        <p>Front Website</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
