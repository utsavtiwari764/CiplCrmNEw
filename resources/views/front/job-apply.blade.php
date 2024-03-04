@extends('layouts.front')
<style>
.header.inner-header{
    height: 435px;
}

@media (max-width:767px) {
    .header.inner-header {
        height: 224px;
    }
}
 .required:after {
    content: " *";
    color: red;
}
.has-error .form-control {
   border-color: #ff0000;
}

.margin-left {
    margin-left: 100px;
}

</style>
@section('header-text')
    <h1 class="hidden-sm-down text-white fs-40 mb-10">{{ ucwords($job->title) }}</h1>
    <h3 class="hidden-sm-up text-white  mb-10">{{ ucwords($job->title) }}</h3>
    <div class="text-white">
        <a class="text-white" href="{{ route('jobs.jobOpenings') }}"><u>@lang('modules.front.jobOpenings')</u>&nbsp; </a> &raquo; &nbsp;<span class="text-white">{{ ucwords($job->title) }}</span>
    </div>
@endsection
<!-- @section('header-text')
    <h1 class="hidden-sm-down">{{ ucwords($job->title) }}</h1>
    <h5 class="hidden-sm-down"><i class="icon-map-pin"></i> {{ ucwords($job->location->location) }}</h5>
@endsection -->

@push('header-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/datepicker3.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')
    @php
        $gender = [
            'male' => __('modules.front.male'),
            'female' => __('modules.front.female'),
            'others' => __('modules.front.others')
        ];
    @endphp

    <form id="createForm" method="POST">
        @csrf
        <input type="hidden" name="job_id" value="{{ $job->id }}">

        <div class="container">
        <div class="row gap-y">
            <!-- <div class="col-md-12 fs-12 pt-50 pb-10 bb-1 mb-20">
                <a class="text-dark"
                   href="{{ route('jobs.jobOpenings') }}">@lang('modules.front.jobOpenings')</a> &raquo; <a
                        class="text-dark"
                        href="{{ route('jobs.jobDetail', $job->slug) }}">{{ ucwords($job->title) }}</a> &raquo; <span
                        class="theme-color">@lang('modules.front.applicationForm')</span>
            </div> -->

            <div class="col-md-4 px-20 pb-30 mt-50">
                <h5 class="required">@lang('modules.front.personalInformation')</h5>
            </div>


            <div class="col-md-8 pb-30 mt-50">

                <div class="form-group">
                    <input class="form-control form-control-lg" type="text" name="full_name" placeholder="@lang('modules.front.fullName')" value="@if($user) {{ $user->name }} @endif">
                </div>

                <div class="form-group">
                    <input class="form-control form-control-lg" type="email" name="email"
                            placeholder="@lang('modules.front.email')" value="@if($user) {{ $user->email }} @endif">
                </div>

                <div class="form-group">
                    <input class="form-control form-control-lg" type="tel" name="phone"
                            placeholder="@lang('modules.front.phone')">
                </div>

            @if ($job->required_columns['address'])
            <div class="form-group">
                <input class="form-control" type="text" name="address"
                        placeholder="@lang('app.address')" autocomplete="none">
            </div>
        @endif

                @if ($job->required_columns['gender'])
                    <label class="control-label" class="required">@lang('modules.front.gender')</label>
                    <div class="form-group">
                        <div class="form-inline">
                            @foreach ($gender as $key => $value)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="{{ $key }}" value="{{ $key }}">
                                    <label class="form-check-label" for="{{ $key }}">{{ ucFirst($value) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if ($job->required_columns['dob'])
                    <div class="form-group">
                        <input class="form-control form-control-lg dob" type="text" name="dob"
                                placeholder="@lang('modules.front.dob')" autocomplete="none">
                    </div>
                @endif
                @if ($job->required_columns['country'])
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="select2 countries" name="country" id="countryId">
                                    <option value="0">@lang('modules.front.selectCountry')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="select2 states" name="state" id="stateId">
                                    <option value="0">@lang('modules.front.selectState')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control form-control-lg" type="text" name="city" id="cityId" placeholder="@lang('modules.front.selectCity')">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control form-control-lg" type="number" name="zip_code" id="zipCode" placeholder="@lang('modules.front.zipCode')">
                            </div>
                        </div>
                    </div>
                @endif
                @if($job->section_visibility['profile_image'] == 'yes')
                    <div class="form-group">
                        <h6>@lang('modules.front.photo')</h6>
                        <img src="@if($user) {{ $user->avatar }} @endif">
                        <input type="hidden" name="linkedinPhoto" value="@if($user) {{ $user->avatar }} @endif">
                        @if($user)
                            <input type="hidden" name="apply_type" value="linkedin">
                        @endif
                         <input class="select-file" accept=".png,.jpg,.jpeg" type="file" name="photo"><br>
                        
                    </div>
                @endif
            </div>

            @if ($job->section_visibility['resume'] == 'yes')
                <div class="col-md-4 px-20 pb-30 bt-1">
                    <h5 class="required">@lang('modules.front.resume')</h5>
                </div>
                <div class="col-md-8 py-30 bt-1">
                    <div class="form-group">
                        <input class="select-file" accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" name="resume"><br>
                        <span>@lang('modules.front.resumeFileType')</span>
                    </div>
                </div>
            @endif
            @if(count($jobQuestion) > 0)
                <div class="col-md-4 px-20 pb-30 bt-1">
                    <h5>@lang('modules.front.additionalDetails')</h5>
                </div>

                <div class="col-md-8 pb-30 bt-1">
                    @forelse($jobQuestion as $question)
                        <div class="form-group">
                            <label class="control-label" for="answer[{{ $question->id}}]">{{ $question->question }}</label><br>
                            @if($question->type == 'text')
                            <input class="form-control form-control-lg" type="text" id="answer[{{ $question->id }}]" name="answer[{{ $question->id }}]" placeholder="@lang('modules.front.yourAnswer')">
                            @else
                            <input type="hidden" name="answer[{{ $question->id}}]">
                            <input class="select-file " accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file"  id="answer[{{ $question->id }}]" name="answer[{{ $question->id }}]"><br>
                            <span>@lang('modules.front.resumeFileType')</span>
                        @endif
                        </div>
                    @empty
                    @endforelse
                </div>
            @endif

            @if ($job->section_visibility['cover_letter'] == 'yes')
                <div class="col-md-4 px-20 pb-30 bt-1">
                    <h5>@lang('modules.front.coverLetter')</h5>
                </div>
                <div class="col-md-8 py-30 bt-1">
                    <div class="form-group">
                        <textarea class="form-control form-control-lg" name="cover_letter" rows="4"></textarea>
                    </div>
                </div>
            @endif

            @if ($job->section_visibility['terms_and_conditions'] == 'yes')
                <div class="col-md-4 px-20 pb-30 bt-1">
                    <h5 class="required">@lang('modules.front.legalTerm')</h5>
                </div>
                <div class="col-md-8 py-30 bt-1">
                    <div class="form-group">
                        <div class="form-control form-control-lg legal-term">
                            {!! $applicationSetting->legal_term !!}
                        </div>
                    </div>
                    <div class="form-group mt-30">
                        <div class="switchery-demo mr-20 d-inline-block" >
                            <input type="checkbox" class="js-switch clearfix" id="agree_term"  value="yes"  data-size="small"  name="term_agreement"  data-color="#00c292" />
                            <label for="term_agreement" class="align-top" ><b>@lang('modules.front.agreeWithTerm')</b></label>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-md-12 pb-30">
                <div class="row">
                    <div class="col-md-8 offset-md-4 margin-left">
                        <button class="btn btn-lg btn-primary btn-block theme-background" id="save-form" type="button">@lang('modules.front.submitApplication')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
    <script>
        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });
    </script>
    <script>
        const fetchCountryState = "{{ route('jobs.fetchCountryState') }}";
        const csrfToken = "{{ csrf_token() }}";
        const selectCountry = "@lang('modules.front.selectCountry')";
        const selectState = "@lang('modules.front.selectState')";
        const selectCity = "@lang('modules.front.selectCity')";
        const pleaseWait = "@lang('modules.front.pleaseWait')";

        let country = "";
        let state = "";
    </script>
    <script src="{{ asset('front/assets/js/location.js') }}"></script>
    <script>
        $('.dob').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            endDate: (new Date()).toDateString(),
        });

        
        $('.select2').select2({
            width: '100%'
        });
        
        $('.form-group span.select2.select2-container').addClass('form-control form-control-lg');

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('jobs.saveApplication')}}',
                container: '#createForm',
                type: "POST",
                file:true,
                redirect: true,
                // data: $('#createForm').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        var successMsg = '<div class="alert alert-success my-100" role="alert">' +
                            response.msg + ' <a class="" href="{{ route('jobs.jobOpenings') }}">@lang("app.view") @lang("modules.front.jobOpenings") <i class="fa fa-arrow-right"></i></a>'
                            '</div>';
                        $('.main-content .container').html(successMsg);
                    }
                },
                error: function (response) {
                   // console.log(response.responseText);
                    handleFails(response);
                }
            })
        });

        function handleFails(response) {
            if (typeof response.responseJSON.errors != "undefined") {
                var keys = Object.keys(response.responseJSON.errors);
                // $('#createForm').find(".has-error").find(".help-block").remove();
                // $('#createForm').find(".has-error").removeClass("has-error");
                $('#createForm').find(".invalid-feedback").remove();
                $('#createForm').find(".is-invalid").removeClass("is-invalid");

                for (var i = 0; i < keys.length; i++) {
                    // Escape dot that comes with error in array fields
                    var key = keys[i].replace(".", '\\.');
                    var formarray = keys[i];

                    // If the response has form array
                    if(formarray.indexOf('.') >0){
                        var array = formarray.split('.');
                        response.responseJSON.errors[keys[i]] = response.responseJSON.errors[keys[i]];
                        key = array[0]+'['+array[1]+']';
                    }

                    var ele = $('#createForm').find("[name='" + key + "']");

                    var grp = ele.closest(".form-group");
                    $(grp).find(".help-block").remove();

                    //check if wysihtml5 editor exist
                    var wys = $(grp).find(".wysihtml5-toolbar").length;

                    if(wys > 0){
                        var helpBlockContainer = $(grp);
                    }
                    else{
                        var helpBlockContainer = $(grp).find("div:first");
                    }
                    if($(ele).is(':radio')){
                        helpBlockContainer = $(grp);
                    }

                    if (helpBlockContainer.length == 0) {
                        helpBlockContainer = $(grp);
                    }

                    helpBlockContainer.append('<div class="help-block">' + response.responseJSON.errors[keys[i]] + '</div>');
                    $(grp).addClass("has-error");
                }

                if (keys.length > 0) {
                    var element = $("[name='" + keys[0] + "']");
                    if (element.length > 0) {
                        $("html, body").animate({scrollTop: element.offset().top - 150}, 200);
                    }
                }
            }
        }
    </script>
@endpush