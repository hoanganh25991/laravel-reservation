@push('css')
<style>
    #toast {
        transition: all 0.8s ease-in-out;
        transform: translateX(415px) scale(0, 1);
        position: fixed;
        top: 20px;
        right: 10px;
        width: 400px;
        height: 80px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, .5);
        background-color: white;
        border-radius: 3px;
        padding: 5px 15px;
        box-sizing: border-box;
        padding: 0px;
    }

    #toast_logo {

    }

    #toast_content {
        display: inline-block;
        width: 300px;
        height: 80px;
        box-sizing: border-box;
        position: absolute;
    }
</style>
@endpush
<div id='toast' :style="(toast.type == 'danger') ? 'background-color: red;' : ''">
    <div style="display: inline-block">
        <img id="toast_logo" src="{{ url('images/cube.svg') }}">
    </div>
    @verbatim
    <div id="toast_content">
        <h4>{{ toast.title }}</h4>
        <p>{{ toast.content }}</p>
    </div>
    @endverbatim
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
                    }, 2000);
                }

                hide(){
                    this.toast.style.transform = 'translateX(415px) scale(0,1)';
                }
            }
            window.Toast = new Toast();
        })();
    });
</script>
@endpush
