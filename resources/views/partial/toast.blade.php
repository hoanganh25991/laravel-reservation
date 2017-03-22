@push('css')
<style>
    #toast {
        transition: all 0.8s ease-in-out;
        transform: translateX(315px) scale(0, 1);
        position: fixed;
        top: 20px;
        right: 10px;
        width: 300px;
        height: 80px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, .5);
        background-color: white;
        border-radius: 3px;
    }
</style>
@endpush
<div id='toast'>
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
    document.addEventListener('vue-mounted', function(){
        console.log('see vue-mounted');
        (function(){
            class Toast {
                constructor(){
                    this.toast = document.querySelector('#toast');

                    /**
                     * Clear setTimeout to auto hide
                     * Which multiple show > cpu halt
                     */
                    this.auto_hide = null;
                }

                show(){
                    this.toast.style.transform = 'translateX(0px) scale(1,1)';
                    let self = this;

                    /**
                     * Incase continuous run
                     * Clear on the previous if not yet
                     */
                    if(self.auto_hide){
                        clearTimeout(self.auto_hide);
                    }
                    this.auto_hide = setTimeout(function(){
                        self.hide();
                        clearTimeout(self.auto_hide);
                    }, 3000);
                }

                hide(){
                    this.toast.style.transform = 'translateX(315px) scale(0,1)';
                }
            }
            window.Toast = new Toast();
        })();
    });
</script>
@endpush
