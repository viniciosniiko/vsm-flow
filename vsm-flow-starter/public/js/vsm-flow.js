(function(){
    if (!window.VSM_FLOW_DATA) return;

    const screen = document.getElementById('chat-screen');
    const restart = document.getElementById('restart-simulation');
    const blocks = window.VSM_FLOW_DATA.blocks || [];
    const connections = window.VSM_FLOW_DATA.connections || [];

    function getStartBlock(){
        return blocks.find(block => block.is_start) || blocks[0];
    }

    function addBubble(text, type){
        const div = document.createElement('div');
        div.className = 'bubble ' + type;
        div.innerText = text || '';
        screen.appendChild(div);
        screen.scrollTop = screen.scrollHeight;
    }

    function getNext(block, option){
        return connections.find(conn => {
            const sameOrigin = Number(conn.from_block_id) === Number(block.id);
            const noCondition = !conn.condition_label;
            const sameCondition = String(conn.condition_label || '').trim().toLowerCase() === String(option || '').trim().toLowerCase();
            return sameOrigin && (sameCondition || noCondition);
        });
    }

    function renderBlock(block){
        if (!block) return;

        setTimeout(() => {
            addBubble(block.message || block.title, 'bot');

            const options = block.options || [];
            if (options.length) {
                const wrap = document.createElement('div');
                wrap.className = 'chat-options';

                options.forEach(option => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.innerText = option;
                    btn.onclick = function(){
                        addBubble(option, 'user');
                        wrap.remove();
                        const nextConn = getNext(block, option);
                        const nextBlock = blocks.find(item => Number(item.id) === Number(nextConn?.to_block_id));
                        renderBlock(nextBlock);
                    };
                    wrap.appendChild(btn);
                });

                screen.appendChild(wrap);
                screen.scrollTop = screen.scrollHeight;
            } else {
                const nextConn = getNext(block, null);
                const nextBlock = blocks.find(item => Number(item.id) === Number(nextConn?.to_block_id));
                if (nextBlock) renderBlock(nextBlock);
            }
        }, 350);
    }

    function start(){
        screen.innerHTML = '';
        renderBlock(getStartBlock());
    }

    restart?.addEventListener('click', start);
    start();
})();
