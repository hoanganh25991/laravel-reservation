@push('css')
<style>
    #redux-state {
        display: block;
        position: relative;
        position: fixed;
        bottom: 0;
        right: 0;
        overflow: scroll;
        /*height: 500px;*/
        /*width: 380px;*/
        height: 0;
        width: 0;
        transition: all 0.5s ease-in-out;
        background-color: #FDFDFD;
    }
    #redux-state pre {
        /*overflow: scroll;*/
        /*height: 500px;*/
        /*transition: all 0.5s ease-in-out;*/
    }
    /*#redux-state svg {*/
    #redux-state-svg {
        position: fixed;
        bottom: 0;
        right: 0;
        /*float: right;*/
    }
</style>
@endpush
<pre id="redux-state"></pre>
<svg id='redux-state-svg' xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <path d="M18.546 3h-13.069l-5.477 8.986v9.014h24v-9.014l-5.454-8.986zm-3.796 12h-5.5l-2.25-3h-4.666l4.266-7h10.82l4.249 7h-4.669l-2.25 3z"/>
</svg>
<script>
    (function(){
//        let svg = document.querySelector('#redux-state svg');
        let svg = document.querySelector('#redux-state-svg');
//        let div = document.querySelector('#redux-state div');
        let div = document.querySelector('#redux-state');

        div.toggleHeight = function(){
            let bound = div.getBoundingClientRect();
            let new_height = '0';
            let new_width  = '0';
            if(bound.height < 37){
                new_height = '500px';
                new_width  = '380px';
            }

            div.style.height = new_height;
            div.style.width  = new_width;
        }

        svg.addEventListener('click', function(){
            console.log('see you click');
            div.toggleHeight();
        });
    })();
</script>
@push('script')
<script src="{{ url_mix('js/syntax-highlight.js') }}"></script>
@endpush
