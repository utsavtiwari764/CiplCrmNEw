<style>
    .hide-box {
        display: none;
    }

    .required:after {
    content:" *";
    color: red;
  }
</style>

<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}">
<div class="modal-header">
    <h4 class="modal-title"><i class="icon-plus"></i> @lang('modules.front.jobAlert')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<div class="modal-body">
   
    <form id="createJobAlert">
        {{ csrf_field() }}
       
            <div class="row">
                <div class="col-md-12 ">
                    <div class="form-group">
                        <label class="required">@lang('menu.jobCategory')</label>
                        <select class=" select2 form-control select2-multiple" name="jobCategory[]" id="jobCategory" multiple="multiple"> 
                       @foreach ($jobCategorys as $jobCategory ) 
                           <option value="{{ $jobCategory->id}}">{{ ucfirst($jobCategory->name)}}</option>
                       @endforeach
                    </select>
                         </div>
                    </div>
                <div class="col-md-12 ">
                    <div class="form-group">
                        <label class="required">@lang('app.location')</label>
                        <select name="location[]" class="select2 form-control select2-multiple" id="location" placeholder="@lang('app.location')" multiple="multiple">
                                
                            @foreach ($locations as $location)
                                <option value="{{ $location->id}}">{{ ucfirst($location->location) }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('modules.jobs.workExperience')</label>
                            <select name="workExperience" id="workExperience" class="form-control">
                                <option value="">--</option>
                                @foreach ($workExperiences as $workExperience)
                                <option value="{{ $workExperience->id}}">{{ ucfirst($workExperience->work_experience) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('modules.jobs.jobType')</label>
                            <select name="jobType" id="jobType" class="form-control">
                                <option value="">--</option>
                                @foreach ($jobTypes as $jobType)
                                <option value="{{ $jobType->id}}">{{ ucfirst($jobType->job_type) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="required">@lang('app.email')</label>
                            <input type="email" name="email" class="form-control" value="">
                        </div>
                    </div>
            </div>
        <div class="text-center">
            <button type="button" id="save-job-alert" class="btn btn-success "> <i class="fa fa-check"></i> @lang('app.submit')</button>
        </div>
    </form>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript">
</script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
<script>
    // Select 2 init.
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $(".select2").select2();

    // Save department
    $('#save-job-alert').click(function () {
        
        $.easyAjax({
            url: '{{route('jobs.saveJobAlert')}}',
            container: '#createJobAlert',
            type: "POST",
            data: $('#createJobAlert').serialize(),
            success: function (response) {
                $('#addJobAlert').modal('hide');

                }
            
        })
    })

   
</script>
