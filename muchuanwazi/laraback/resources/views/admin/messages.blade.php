@inject('messageAlert', 'App\Services\Common\MessageAlert')
@if(($message=$messageAlert->getAlert())!==false)
    <div class="row">
        <div class="col-md-12"><!--danger,info,warning,success-->
            <div class="auto-close callout callout-{{$message['messageType']}}">
                <h4>{{$message['messageTitle']}}</h4>

                <p>{{$message['messageBody']}}</p>
            </div>
        </div>
    </div>
    @push('runningScripts')
    <script>
        $(function () {
            setTimeout(function () {
                $('.auto-close').slideUp();
            }, 3000);

        });
    </script>
    @endpush
@endif