@push('css')
    <style>
        #toast {
            transition: all 0.8s ease-in-out;
            transform: translateX(315px) scale(0, 1);
        }
    </style>
@endpush
<div id='toast' style="position: fixed; top: 20px; right: 10px; width: 300px; height: 80px; box-shadow: 0 5px 15px rgba(0,0,0,.5); background-color: white; border-radius: 3px">
    <div class="row">
        <div class="col-xs-4">
            <img src="{{ url('images/cube.svg') }}" class="img-responsive" alt="Cinque Terre">
        </div>
        @verbatim
        <div class="col-xs-8" style="padding: 0">
            <h4>{{ toast.title }}</h4>
            <p><span>{{ toast.content }}</span></p>
        </div>
        @endverbatim
    </div>
</div>
@push('script')
    <script>
        (function(){
            class Toast {
                constructor(){
                    this.toast = document.querySelector('#toast');
                }

                show(){
                    this.toast.style.transform = 'translateX(0px) scale(1,1)';
                }

                hide(){
                    this.toast.style.transform = 'translateX(315px) scale(0,1)';
                }
            }
            window.Toast = new Toast();
        })()
    </script>
@endpush
