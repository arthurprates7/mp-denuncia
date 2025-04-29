class StreamHandler {
    constructor(url, onMessage, onComplete) {
        this.url = url;
        this.onMessage = onMessage;
        this.onComplete = onComplete;
        this.eventSource = null;
        this.isStreaming = false;
    }

    start() {
        if (this.isStreaming) return;
        
        this.isStreaming = true;
        this.eventSource = new EventSource(this.url);
        
        this.eventSource.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                
                if (data.done) {
                    this.stop();
                    if (this.onComplete) this.onComplete();
                    return;
                }
                
                if (data.text !== undefined && this.onMessage) {
                    this.onMessage(data.text);
                }
            } catch (error) {
                console.error('Erro ao processar mensagem:', error);
            }
        };
        
        this.eventSource.onerror = (error) => {
            console.error('EventSource failed:', error);
            this.stop();
        };
    }

    stop() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
        this.isStreaming = false;
    }
}

// Função para iniciar o streaming para partes
function startStreamingPartes(instrucoes, numeroCnj, targetElementId) {
    const url = `/api/stream-partes?instrucoes=${encodeURIComponent(instrucoes)}&numero_cnj=${encodeURIComponent(numeroCnj)}`;
    const outputElement = document.getElementById(targetElementId);
    
    // Limpa o conteúdo anterior
    outputElement.value = '';
    
    const streamHandler = new StreamHandler(
        url,
        (text) => {
            if (text !== undefined && text !== null) {
                // Adiciona o texto ao campo
                outputElement.value += text;
                // Rola para o final do texto
                outputElement.scrollTop = outputElement.scrollHeight;
            }
        },
        () => {
            console.log('Streaming de partes concluído');
        }
    );
    
    streamHandler.start();
    return streamHandler;
}

// Função para iniciar o streaming para fatos
function startStreamingFatos(instrucoes, numeroCnj, targetElementId) {
    const url = `/api/stream-fatos?instrucoes=${encodeURIComponent(instrucoes)}&numero_cnj=${encodeURIComponent(numeroCnj)}`;
    const outputElement = document.getElementById(targetElementId);
    
    // Limpa o conteúdo anterior
    outputElement.value = '';
    
    const streamHandler = new StreamHandler(
        url,
        (text) => {
            if (text !== undefined && text !== null) {
                // Adiciona o texto ao campo
                outputElement.value += text;
                // Rola para o final do texto
                outputElement.scrollTop = outputElement.scrollHeight;
            }
        },
        () => {
            console.log('Streaming de fatos concluído');
        }
    );
    
    streamHandler.start();
    return streamHandler;
}

// Função para iniciar o streaming para quota
function startStreamingQuota(instrucoes, numeroCnj, targetElementId) {
    const url = `/api/stream-quota?instrucoes=${encodeURIComponent(instrucoes)}&numero_cnj=${encodeURIComponent(numeroCnj)}`;
    const outputElement = document.getElementById(targetElementId);
    
    // Limpa o conteúdo anterior
    outputElement.value = '';
    
    const streamHandler = new StreamHandler(
        url,
        (text) => {
            if (text !== undefined && text !== null) {
                // Adiciona o texto ao campo
                outputElement.value += text;
                // Rola para o final do texto
                outputElement.scrollTop = outputElement.scrollHeight;
            }
        },
        () => {
            console.log('Streaming de quota concluído');
        }
    );
    
    streamHandler.start();
    return streamHandler;
} 