const BLOCK_DEFAULTS = {
    mensagem: { title: 'Nova mensagem', message: 'Olá! Como posso ajudar?', options: [] },
    opcoes: { title: 'Escolha uma opção', message: 'Selecione uma das opções abaixo:', options: ['Opção 1', 'Opção 2'] },
    lista: { title: 'Menu de opções', message: 'Escolha uma opção da lista:', options: ['Item 1', 'Item 2', 'Item 3'] },
    pergunta: { title: 'Pergunta ao paciente', message: 'Por favor, informe o dado solicitado:', options: [] },
    condicao: { title: 'Condição', message: 'Condição interna do fluxo', options: [] },
    api: { title: 'Consulta de API', message: 'Aguarde enquanto consultamos as informações...', options: [] },
    transferencia: { title: 'Transferir para atendente', message: 'Vou transferir seu atendimento para nossa equipe.', options: [] },
    fim: { title: 'Finalizar atendimento', message: 'Atendimento finalizado. Obrigado!', options: [] },
};

const BLOCK_ICONS = {
    mensagem: '💬',
    opcoes: '🔘',
    lista: '📋',
    pergunta: '❓',
    condicao: '◇',
    api: '🌐',
    transferencia: '👤',
    fim: '🏁',
};

export function iniciarConstrutor() {
    const app = document.querySelector('[data-constructor]');
    if (!app) return;

    let initialFlow = { nodes: [], connections: [] };

    try {
        initialFlow = JSON.parse(app.dataset.initialFlow || '{"nodes":[],"connections":[]}');
    } catch (error) {
        console.error('JSON inicial inválido:', error);
    }

    const state = {
        blocks: Array.isArray(initialFlow.nodes) ? initialFlow.nodes : [],
        connections: Array.isArray(initialFlow.connections) ? initialFlow.connections : [],
        selectedId: null,
        connectionOrigin: null,
        zoom: 100,
        nextId: 1,
        simulationCurrentId: null,
    };

    state.nextId = Math.max(0, ...state.blocks.map((block) => Number(block.id) || 0)) + 1;

    const nodesContainer = app.querySelector('[data-flow-nodes]');
    const canvas = app.querySelector('[data-canvas]');
    const canvasEmpty = app.querySelector('[data-canvas-empty]');
    const connectionLayer = app.querySelector('[data-connections-layer]');
    const previewChat = app.querySelector('[data-preview-chat]');
    const simulationForm = app.querySelector('[data-simulation-form]');
    const simulationInput = app.querySelector('[data-simulation-input]');
    const drawer = app.querySelector('[data-property-drawer]');
    const backdrop = app.querySelector('[data-property-backdrop]');
    const titleField = app.querySelector('[data-property-title]');
    const messageField = app.querySelector('[data-property-message]');
    const noteField = app.querySelector('[data-property-note]');
    const optionsEditor = app.querySelector('[data-options-editor]');
    const optionsList = app.querySelector('[data-options-list]');
    const drawerTitle = app.querySelector('[data-drawer-title]');
    const saveStatus = app.querySelector('[data-save-status]');
    const search = app.querySelector('[data-block-search]');
    const blockItems = [...app.querySelectorAll('[data-block-type]')];

    const selectedBlock = () =>
        state.blocks.find((block) => Number(block.id) === Number(state.selectedId)) || null;

    const markDirty = () => {
        saveStatus.textContent = 'Alterações não salvas';
    };

    const escapeHtml = (value = '') =>
        String(value).replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[character]));

    const createBlock = (type) => {
        const defaults = BLOCK_DEFAULTS[type];
        const index = state.blocks.length;

        const block = {
            id: state.nextId++,
            type,
            title: defaults.title,
            message: defaults.message,
            options: [...defaults.options],
            note: '',
            x: 80 + (index % 3) * 285,
            y: 80 + Math.floor(index / 3) * 190,
        };

        state.blocks.push(block);
        markDirty();
        renderAll();
        openDrawer(block);
    };

    const openDrawer = (block) => {
        state.selectedId = Number(block.id);
        titleField.value = block.title || '';
        messageField.value = block.message || '';
        noteField.value = block.note || '';
        drawerTitle.textContent = block.title || 'Bloco';

        const hasOptions = ['opcoes', 'lista'].includes(block.type);
        optionsEditor.classList.toggle('is-hidden', !hasOptions);
        renderOptionsEditor(block);

        drawer.classList.add('is-open');
        backdrop.classList.add('is-open');
        renderNodes();
    };

    const closeDrawer = () => {
        drawer.classList.remove('is-open');
        backdrop.classList.remove('is-open');
    };

    const normalizeConnection = (connection) => ({
        id: connection.id || `${connection.from}-${connection.to}-${Date.now()}`,
        from: Number(connection.from),
        to: Number(connection.to),
        optionIndex: connection.optionIndex === null || connection.optionIndex === undefined
            ? null
            : Number(connection.optionIndex),
        optionText: connection.optionText ?? null,
    });

    state.connections = state.connections.map(normalizeConnection);

    const createConnection = (origin, to) => {
        if (!origin || Number(origin.blockId) === Number(to)) return;

        const exists = state.connections.some((connection) =>
            Number(connection.from) === Number(origin.blockId) &&
            Number(connection.to) === Number(to) &&
            connection.optionIndex === origin.optionIndex
        );

        if (!exists) {
            state.connections.push({
                id: `${origin.blockId}-${origin.optionIndex ?? 'default'}-${to}-${Date.now()}`,
                from: Number(origin.blockId),
                to: Number(to),
                optionIndex: origin.optionIndex,
                optionText: origin.optionText,
            });
            markDirty();
        }
    };

    const connectionFor = (blockId, optionIndex = null) =>
        state.connections.find((connection) =>
            Number(connection.from) === Number(blockId) &&
            connection.optionIndex === optionIndex
        ) || null;

    const renderNodes = () => {
        canvasEmpty.classList.toggle('is-hidden', state.blocks.length > 0);

        nodesContainer.innerHTML = state.blocks.map((block) => {
            const hasIndividualOutputs = ['opcoes', 'lista'].includes(block.type);
            const options = Array.isArray(block.options) ? block.options : [];

            const outputsHtml = hasIndividualOutputs
                ? `
                    <div class="flow-node-options">
                        ${options.map((option, index) => {
                            const connected = connectionFor(block.id, index);
                            const isActive =
                                state.connectionOrigin &&
                                Number(state.connectionOrigin.blockId) === Number(block.id) &&
                                Number(state.connectionOrigin.optionIndex) === index;

                            return `
                                <div class="flow-node-option ${connected ? 'is-connected' : ''}">
                                    <span>${escapeHtml(option || `Opção ${index + 1}`)}</span>
                                    <button
                                        type="button"
                                        class="option-output-port ${isActive ? 'is-connecting' : ''}"
                                        data-option-output="${index}"
                                        title="${connected ? 'Alterar conexão' : 'Conectar esta opção'}"
                                    ></button>
                                </div>
                            `;
                        }).join('')}
                    </div>
                `
                : `
                    <button
                        type="button"
                        class="flow-node-port output ${
                            state.connectionOrigin &&
                            Number(state.connectionOrigin.blockId) === Number(block.id) &&
                            state.connectionOrigin.optionIndex === null
                                ? 'is-connecting'
                                : ''
                        }"
                        data-default-output
                        title="Criar conexão"
                    ></button>
                `;

            return `
                <div
                    class="flow-node ${Number(state.selectedId) === Number(block.id) ? 'is-selected' : ''} ${state.connectionOrigin ? 'connection-mode' : ''}"
                    data-node-id="${block.id}"
                    style="left:${Number(block.x) || 0}px; top:${Number(block.y) || 0}px"
                >
                    <div class="flow-node-header">
                        <span class="flow-node-icon">${BLOCK_ICONS[block.type]}</span>
                        <span class="flow-node-type">${escapeHtml(block.type)}</span>
                        <span class="flow-node-menu">•••</span>
                    </div>

                    <div class="flow-node-content">
                        <strong>${escapeHtml(block.title)}</strong>
                        <p>${escapeHtml(block.message || 'Sem mensagem configurada')}</p>
                    </div>

                    ${outputsHtml}
                </div>
            `;
        }).join('');

        nodesContainer.querySelectorAll('[data-node-id]').forEach((node) => {
            const id = Number(node.dataset.nodeId);
            const block = state.blocks.find((item) => Number(item.id) === id);

            node.addEventListener('click', (event) => {
                if (event.target.closest('[data-default-output], [data-option-output]')) return;

                if (state.connectionOrigin && Number(state.connectionOrigin.blockId) !== id) {
                    createConnection(state.connectionOrigin, id);
                    state.connectionOrigin = null;
                    renderAll();
                    return;
                }

                if (!node.dataset.wasDragged) {
                    openDrawer(block);
                }

                delete node.dataset.wasDragged;
            });

            node.querySelector('[data-default-output]')?.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                const same =
                    state.connectionOrigin &&
                    Number(state.connectionOrigin.blockId) === id &&
                    state.connectionOrigin.optionIndex === null;

                state.connectionOrigin = same
                    ? null
                    : { blockId: id, optionIndex: null, optionText: null };

                renderNodes();
            });

            node.querySelectorAll('[data-option-output]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();

                    const optionIndex = Number(button.dataset.optionOutput);
                    const optionText = block.options?.[optionIndex] ?? `Opção ${optionIndex + 1}`;

                    const same =
                        state.connectionOrigin &&
                        Number(state.connectionOrigin.blockId) === id &&
                        Number(state.connectionOrigin.optionIndex) === optionIndex;

                    state.connectionOrigin = same
                        ? null
                        : { blockId: id, optionIndex, optionText };

                    renderNodes();
                });
            });

            enableDrag(node, block);
        });
    };

    const enableDrag = (node, block) => {
        let dragging = false;
        let moved = false;
        let startX = 0;
        let startY = 0;
        let originalX = 0;
        let originalY = 0;

        const onMove = (event) => {
            if (!dragging) return;

            const distance = Math.abs(event.clientX - startX) + Math.abs(event.clientY - startY);
            if (distance > 4) moved = true;

            const scale = state.zoom / 100;
            block.x = Math.max(0, originalX + (event.clientX - startX) / scale);
            block.y = Math.max(0, originalY + (event.clientY - startY) / scale);

            node.style.left = `${block.x}px`;
            node.style.top = `${block.y}px`;

            renderConnections();
            markDirty();
        };

        const onUp = () => {
            if (moved) {
                node.dataset.wasDragged = '1';
                setTimeout(() => delete node.dataset.wasDragged, 0);
            }

            dragging = false;
            document.body.classList.remove('is-dragging-node');
            document.removeEventListener('pointermove', onMove);
            document.removeEventListener('pointerup', onUp);
        };

        node.addEventListener('pointerdown', (event) => {
            if (event.button !== 0) return;
            if (event.target.closest('[data-default-output], [data-option-output]')) return;
            if (state.connectionOrigin) return;

            event.preventDefault();

            dragging = true;
            moved = false;
            startX = event.clientX;
            startY = event.clientY;
            originalX = Number(block.x) || 0;
            originalY = Number(block.y) || 0;

            document.body.classList.add('is-dragging-node');
            document.addEventListener('pointermove', onMove);
            document.addEventListener('pointerup', onUp);
        });
    };

    const renderConnections = () => {
        if (!connectionLayer || !canvas) return;

        connectionLayer.innerHTML = '';
        const canvasRect = canvas.getBoundingClientRect();

        state.connections.forEach((connection) => {
            const fromNode = nodesContainer.querySelector(`[data-node-id="${connection.from}"]`);
            const toNode = nodesContainer.querySelector(`[data-node-id="${connection.to}"]`);

            if (!fromNode || !toNode) return;

            const sourceElement = connection.optionIndex === null
                ? fromNode.querySelector('[data-default-output]')
                : fromNode.querySelector(`[data-option-output="${connection.optionIndex}"]`);

            if (!sourceElement) return;

            const fromRect = sourceElement.getBoundingClientRect();
            const toRect = toNode.getBoundingClientRect();

            const x1 = fromRect.left + fromRect.width / 2 - canvasRect.left + canvas.scrollLeft;
            const y1 = fromRect.top + fromRect.height / 2 - canvasRect.top + canvas.scrollTop;
            const x2 = toRect.left - canvasRect.left + canvas.scrollLeft;
            const y2 = toRect.top + Math.min(48, toRect.height / 2) - canvasRect.top + canvas.scrollTop;

            const curve = Math.max(70, Math.abs(x2 - x1) * 0.45);
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

            path.setAttribute('d', `M ${x1} ${y1} C ${x1 + curve} ${y1}, ${x2 - curve} ${y2}, ${x2} ${y2}`);
            path.setAttribute('class', 'connection-path');
            path.dataset.connectionId = connection.id;

            path.addEventListener('dblclick', () => {
                state.connections = state.connections.filter((item) => item.id !== connection.id);
                markDirty();
                renderAll();
            });

            connectionLayer.appendChild(path);
        });
    };

    const renderOptionsEditor = (block) => {
        if (!['opcoes', 'lista'].includes(block.type)) {
            optionsList.innerHTML = '';
            return;
        }

        block.options = Array.isArray(block.options) ? block.options : [];

        optionsList.innerHTML = block.options.map((option, index) => {
            const connection = connectionFor(block.id, index);
            const target = connection
                ? state.blocks.find((item) => Number(item.id) === Number(connection.to))
                : null;

            return `
                <div class="option-editor-card">
                    <div class="option-row">
                        <span>${index + 1}</span>
                        <input type="text" value="${escapeHtml(option)}" data-option-index="${index}">
                        <button type="button" data-remove-option="${index}">×</button>
                    </div>
                    <div class="option-connection-status ${target ? 'is-connected' : ''}">
                        ${target
                            ? `Conectado a: <strong>${escapeHtml(target.title)}</strong>`
                            : 'Sem conexão configurada'}
                    </div>
                </div>
            `;
        }).join('');

        optionsList.querySelectorAll('[data-option-index]').forEach((input) => {
            input.addEventListener('input', () => {
                const index = Number(input.dataset.optionIndex);
                block.options[index] = input.value;

                state.connections
                    .filter((connection) =>
                        Number(connection.from) === Number(block.id) &&
                        connection.optionIndex === index
                    )
                    .forEach((connection) => {
                        connection.optionText = input.value;
                    });

                markDirty();
            });
        });

        optionsList.querySelectorAll('[data-remove-option]').forEach((button) => {
            button.addEventListener('click', () => {
                const removedIndex = Number(button.dataset.removeOption);

                block.options.splice(removedIndex, 1);

                state.connections = state.connections
                    .filter((connection) =>
                        !(
                            Number(connection.from) === Number(block.id) &&
                            connection.optionIndex === removedIndex
                        )
                    )
                    .map((connection) => {
                        if (
                            Number(connection.from) === Number(block.id) &&
                            connection.optionIndex !== null &&
                            connection.optionIndex > removedIndex
                        ) {
                            return {
                                ...connection,
                                optionIndex: connection.optionIndex - 1,
                            };
                        }

                        return connection;
                    });

                renderOptionsEditor(block);
                renderAll();
                markDirty();
            });
        });
    };

    const firstBlock = () => {
        if (!state.blocks.length) return null;

        const targetIds = new Set(state.connections.map((connection) => Number(connection.to)));

        return state.blocks.find((block) => !targetIds.has(Number(block.id))) || state.blocks[0];
    };

    const appendBotBlock = (block) => {
        if (!block) return;

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble bot';
        bubble.innerHTML = `
            ${escapeHtml(block.message || block.title)}
            <span class="bubble-time">agora ✓✓</span>
        `;
        previewChat.appendChild(bubble);

        if (Array.isArray(block.options) && block.options.length) {
            const optionGroup = document.createElement('div');
            optionGroup.className = 'preview-options';

            block.options.forEach((option, optionIndex) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = option;

                button.addEventListener('click', () => {
                    appendPatientMessage(option);
                    advanceSimulation(block.id, optionIndex);
                });

                optionGroup.appendChild(button);
            });

            previewChat.appendChild(optionGroup);
        }

        previewChat.scrollTop = previewChat.scrollHeight;
    };

    const appendPatientMessage = (message) => {
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble patient';
        bubble.innerHTML = `
            ${escapeHtml(message)}
            <span class="bubble-time">agora ✓✓</span>
        `;
        previewChat.appendChild(bubble);
        previewChat.scrollTop = previewChat.scrollHeight;
    };

    const nextBlockFrom = (blockId, optionIndex = null) => {
        let connection = null;

        if (optionIndex !== null) {
            connection = state.connections.find((item) =>
                Number(item.from) === Number(blockId) &&
                item.optionIndex === optionIndex
            );
        }

        if (!connection) {
            connection = state.connections.find((item) =>
                Number(item.from) === Number(blockId) &&
                item.optionIndex === null
            );
        }

        return connection
            ? state.blocks.find((block) => Number(block.id) === Number(connection.to))
            : null;
    };

    const advanceSimulation = (fromId, optionIndex = null) => {
        const next = nextBlockFrom(fromId, optionIndex);
        if (!next) return;

        state.simulationCurrentId = Number(next.id);

        setTimeout(() => {
            appendBotBlock(next);

            if (!['pergunta', 'opcoes', 'lista'].includes(next.type)) {
                if (nextBlockFrom(next.id, null)) {
                    setTimeout(() => advanceSimulation(next.id, null), 450);
                }
            }
        }, 300);
    };

    const restartSimulation = () => {
        previewChat.innerHTML = '';
        const start = firstBlock();

        if (!start) {
            previewChat.innerHTML = `
                <div class="preview-empty">
                    <div>💬</div>
                    <strong>A conversa aparecerá aqui</strong>
                    <span>Adicione e conecte os blocos do fluxo.</span>
                </div>
            `;
            state.simulationCurrentId = null;
            return;
        }

        state.simulationCurrentId = Number(start.id);
        appendBotBlock(start);

        if (!['pergunta', 'opcoes', 'lista'].includes(start.type) && nextBlockFrom(start.id, null)) {
            setTimeout(() => advanceSimulation(start.id, null), 450);
        }
    };

    const renderAll = () => {
        renderNodes();
        requestAnimationFrame(renderConnections);
    };

    blockItems.forEach((item) => {
        item.addEventListener('click', () => createBlock(item.dataset.blockType));
    });

    search?.addEventListener('input', (event) => {
        const term = event.target.value.toLowerCase().trim();

        blockItems.forEach((item) => {
            item.style.display = item.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    titleField.addEventListener('input', () => {
        const block = selectedBlock();
        if (!block) return;

        block.title = titleField.value;
        drawerTitle.textContent = block.title || 'Bloco';
        renderNodes();
        requestAnimationFrame(renderConnections);
        markDirty();
    });

    messageField.addEventListener('input', () => {
        const block = selectedBlock();
        if (!block) return;

        block.message = messageField.value;
        renderNodes();
        requestAnimationFrame(renderConnections);
        markDirty();
    });

    noteField.addEventListener('input', () => {
        const block = selectedBlock();
        if (!block) return;

        block.note = noteField.value;
        markDirty();
    });

    app.querySelector('[data-action="add-option"]')?.addEventListener('click', () => {
        const block = selectedBlock();
        if (!block) return;

        block.options.push(`Opção ${block.options.length + 1}`);
        renderOptionsEditor(block);
        renderAll();
        markDirty();
    });

    app.querySelector('[data-action="apply-properties"]')?.addEventListener('click', () => {
        closeDrawer();
        restartSimulation();
    });

    app.querySelector('[data-action="delete-node"]')?.addEventListener('click', () => {
        if (!state.selectedId) return;

        state.blocks = state.blocks.filter((block) => Number(block.id) !== Number(state.selectedId));

        state.connections = state.connections.filter((connection) =>
            Number(connection.from) !== Number(state.selectedId) &&
            Number(connection.to) !== Number(state.selectedId)
        );

        state.selectedId = null;
        closeDrawer();
        markDirty();
        renderAll();
        restartSimulation();
    });

    app.querySelector('[data-action="close-properties"]')?.addEventListener('click', closeDrawer);
    backdrop.addEventListener('click', closeDrawer);
    app.querySelector('[data-action="restart-simulation"]')?.addEventListener('click', restartSimulation);

    simulationForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const message = simulationInput.value.trim();
        if (!message) return;

        appendPatientMessage(message);
        simulationInput.value = '';

        if (state.simulationCurrentId) {
            advanceSimulation(state.simulationCurrentId, null);
        }
    });

    app.querySelector('[data-action="save"]')?.addEventListener('click', async () => {
        saveStatus.textContent = 'Salvando...';

        try {
            const response = await fetch(app.dataset.saveUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    nodes: state.blocks,
                    connections: state.connections,
                }),
            });

            if (!response.ok) {
                throw new Error('Não foi possível salvar o fluxo.');
            }

            const result = await response.json();
            saveStatus.textContent = `Salvo em ${result.saved_at}`;
        } catch (error) {
            console.error(error);
            saveStatus.textContent = 'Erro ao salvar';
            alert('O fluxo não pôde ser salvo. Confira o console.');
        }
    });

    const updateZoom = () => {
        app.querySelector('[data-zoom-label]').textContent = `${state.zoom}%`;
        nodesContainer.style.transform = `scale(${state.zoom / 100})`;
        nodesContainer.style.transformOrigin = 'top left';
        requestAnimationFrame(renderConnections);
    };

    app.querySelector('[data-action="zoom-in"]')?.addEventListener('click', () => {
        state.zoom = Math.min(150, state.zoom + 10);
        updateZoom();
    });

    app.querySelector('[data-action="zoom-out"]')?.addEventListener('click', () => {
        state.zoom = Math.max(50, state.zoom - 10);
        updateZoom();
    });

    app.querySelector('[data-action="center"]')?.addEventListener('click', () => {
        state.zoom = 100;
        updateZoom();
        canvas.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
    });

	// ======================================================
	// NAVEGAÇÃO DO CANVAS - ESTILO MIRO
	// ======================================================

	let isPanning = false;
	let panStartX = 0;
	let panStartY = 0;
	let panStartScrollLeft = 0;
	let panStartScrollTop = 0;

	const stopPanning = (event = null) => {
		if (!isPanning) return;

		isPanning = false;
		canvas.classList.remove('is-panning');

		if (
			event &&
			canvas.hasPointerCapture &&
			canvas.hasPointerCapture(event.pointerId)
		) {
			canvas.releasePointerCapture(event.pointerId);
		}
	};

	canvas.addEventListener('pointerdown', (event) => {

		// Somente botão esquerdo
		if (event.button !== 0) return;

		// Se clicar em bloco ou controle, NÃO move o canvas
		if (
			event.target.closest(
				'[data-node-id], ' +
				'[data-default-output], ' +
				'[data-option-output], ' +
				'button, input, textarea, select, a'
			)
		) {
			return;
		}

		isPanning = true;

		panStartX = event.clientX;
		panStartY = event.clientY;

		panStartScrollLeft = canvas.scrollLeft;
		panStartScrollTop = canvas.scrollTop;

		canvas.classList.add('is-panning');

		if (canvas.setPointerCapture) {
			canvas.setPointerCapture(event.pointerId);
		}

		event.preventDefault();
	});

	canvas.addEventListener('pointermove', (event) => {

		if (!isPanning) return;

		const deltaX = event.clientX - panStartX;
		const deltaY = event.clientY - panStartY;

		canvas.scrollLeft = panStartScrollLeft - deltaX;
		canvas.scrollTop = panStartScrollTop - deltaY;
	});

	canvas.addEventListener('pointerup', stopPanning);
	canvas.addEventListener('pointercancel', stopPanning);

	// SHIFT + SCROLL = movimento horizontal
	canvas.addEventListener(
		'wheel',
		(event) => {

			if (!event.shiftKey) return;

			event.preventDefault();

			canvas.scrollLeft += event.deltaY || event.deltaX;
		},
		{ passive: false }
	);
	
	
    canvas.addEventListener('scroll', () => requestAnimationFrame(renderConnections));
    window.addEventListener('resize', () => requestAnimationFrame(renderConnections));

    renderAll();
    restartSimulation();
}
