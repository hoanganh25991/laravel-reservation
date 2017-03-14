<style>
    #redux-state {
        position: fixed;
        bottom: 0;
        right: 0;
    }
    #redux-state pre {
        overflow: scroll;
        height: 500px;
        transition: all 0.5s ease-in-out;
    }
    #redux-state svg {
        float: right;
    }
</style>
<div id="redux-state">
    <pre id="expand"></pre>
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
        <path d="M18.546 3h-13.069l-5.477 8.986v9.014h24v-9.014l-5.454-8.986zm-3.796 12h-5.5l-2.25-3h-4.666l4.266-7h10.82l4.249 7h-4.669l-2.25 3z"/>
    </svg>
</div>
<script>
    (function(){
        let svg = document.querySelector('#redux-state svg');
        let pre = document.querySelector('#redux-state pre');

        pre.toggleHeight = function(){
            let bound = pre.getBoundingClientRect();
            let new_height = '12px';
            if(bound.height < 22)
                new_height = '500px';

            pre.style.height = new_height;
        }

        svg.addEventListener('click', function(){
            pre.toggleHeight();
        });
    })();
</script>
