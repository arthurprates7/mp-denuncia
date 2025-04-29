document.addEventListener('DOMContentLoaded', function() {
    // Função para ajustar a altura do textarea
    function ajustarAlturaTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight) + 'px';
    }

    // First field (Quota)
    const instructionsTextarea1 = document.getElementById('instrucoes-gpt');
    const generateButton1 = document.getElementById('gerar-trecho');
    const generatedText1 = document.getElementById('texto-gerado-quota');
    const finalText1 = document.getElementById('texto-editado-quota');
    const approveButton1 = document.getElementById('aprovar-quota');
    const pdfIframe = document.getElementById('pdf-viewer');

    // Adicionar evento de input para ajustar altura
    generatedText1.addEventListener('input', function() {
        ajustarAlturaTextarea(this);
    });

    instructionsTextarea1.addEventListener('input', function() {
        const hasText = this.value.trim();
        generateButton1.disabled = !hasText;
        generateButton1.classList.toggle('btn-primary', hasText);
        generateButton1.classList.toggle('btn-secondary', !hasText);
    });

    generateButton1.addEventListener('click', function() {
        const instructions = instructionsTextarea1.value.trim();
        const numeroCnj = new URLSearchParams(window.location.search).get('numero_cnj');
        
        // Show loading on button
        const originalText = generateButton1.innerHTML;
        generateButton1.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gerando...';
        generateButton1.disabled = true;

        // Clear previous text
        generatedText1.value = '';
        ajustarAlturaTextarea(generatedText1);

        // Create SSE connection
        const eventSource = new EventSource(`${routes.streamQuota}?instrucoes=${encodeURIComponent(instructions)}&numero_cnj=${encodeURIComponent(numeroCnj)}`);

        eventSource.onmessage = function(event) {
            try {
                const data = JSON.parse(event.data);
                
                if (data.done) {
                    eventSource.close();
                    generateButton1.innerHTML = originalText;
                    generateButton1.disabled = false;
                    
                    // Update PDF with generated text
                    pdfIframe.src = `${routes.getPdf}?quota=${encodeURIComponent(generatedText1.value)}`;
                    return;
                }
                
                if (data.text && typeof data.text === 'string') {
                    generatedText1.value += data.text;
                    generatedText1.scrollTop = generatedText1.scrollHeight;
                    ajustarAlturaTextarea(generatedText1);
                }
            } catch (e) {
                console.error('Erro ao processar mensagem:', e);
            }
        };

        eventSource.onerror = function(error) {
            console.error('Erro na conexão:', error);
            eventSource.close();
            generateButton1.innerHTML = originalText;
            generateButton1.disabled = false;
            alert('Erro ao gerar texto. Por favor, tente novamente.');
        };
    });

    approveButton1.addEventListener('click', function() {
        // Mark as approved
        const icon = this.querySelector('i');
        icon.classList.toggle('text-success');
        icon.classList.toggle('text-white');
        
        // Update PDF with approved text
        pdfIframe.src = `${routes.getPdf}?quota=${encodeURIComponent(finalText1.value)}`;
    });

    // Second field (Parts)
    const instructionsTextarea2 = document.getElementById('instrucoes-gpt-partes');
    const generateButton2 = document.getElementById('gerar-trecho-partes');
    const generatedText2 = document.getElementById('texto-gerado-partes');
    const finalText2 = document.getElementById('texto-editado-partes');
    const approveButton2 = document.getElementById('aprovar-partes');

    // Adicionar evento de input para ajustar altura
    generatedText2.addEventListener('input', function() {
        ajustarAlturaTextarea(this);
    });

    instructionsTextarea2.addEventListener('input', function() {
        const hasText = this.value.trim();
        generateButton2.disabled = !hasText;
        generateButton2.classList.toggle('btn-primary', hasText);
        generateButton2.classList.toggle('btn-secondary', !hasText);
    });

    generateButton2.addEventListener('click', function() {
        const instructions = instructionsTextarea2.value.trim();
        const numeroCnj = new URLSearchParams(window.location.search).get('numero_cnj');
        
        // Show loading on button
        const originalText = generateButton2.innerHTML;
        generateButton2.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gerando...';
        generateButton2.disabled = true;

        // Clear previous text
        generatedText2.value = '';
        ajustarAlturaTextarea(generatedText2);

        // Create SSE connection
        const eventSource = new EventSource(`${routes.streamPartes}?instrucoes=${encodeURIComponent(instructions)}&numero_cnj=${encodeURIComponent(numeroCnj)}`);

        eventSource.onmessage = function(event) {
            try {
                const data = JSON.parse(event.data);
                
                if (data.done) {
                    eventSource.close();
                    generateButton2.innerHTML = originalText;
                    generateButton2.disabled = false;
                    
                    // Update PDF with final text
                    pdfIframe.src = `${routes.getPdf}?quota=${encodeURIComponent(finalText1.value)}&partes=${encodeURIComponent(finalText2.value)}`;
                    return;
                }
                
                if (data.text && typeof data.text === 'string') {
                    generatedText2.value += data.text;
                    generatedText2.scrollTop = generatedText2.scrollHeight;
                    ajustarAlturaTextarea(generatedText2);
                }
            } catch (e) {
                console.error('Erro ao processar mensagem:', e);
            }
        };

        eventSource.onerror = function(error) {
            console.error('Erro na conexão:', error);
            eventSource.close();
            generateButton2.innerHTML = originalText;
            generateButton2.disabled = false;
            alert('Erro ao gerar texto. Por favor, tente novamente.');
        };
    });

    approveButton2.addEventListener('click', function() {
        // Mark as approved
        const icon = this.querySelector('i');
        icon.classList.toggle('text-success');
        icon.classList.toggle('text-white');
        
        // Update PDF with approved text
        pdfIframe.src = `${routes.getPdf}?quota=${encodeURIComponent(finalText1.value)}&partes=${encodeURIComponent(finalText2.value)}`;
    });

    // Third field (Facts)
    const instructionsTextarea3 = document.getElementById('instrucoes-gpt-fatos');
    const generateButton3 = document.getElementById('gerar-trecho-fatos');
    const generatedText3 = document.getElementById('texto-gerado-fatos');
    const finalText3 = document.getElementById('texto-editado-fatos');
    const approveButton3 = document.getElementById('aprovar-fatos');

    // Adicionar evento de input para ajustar altura
    generatedText3.addEventListener('input', function() {
        ajustarAlturaTextarea(this);
    });

    instructionsTextarea3.addEventListener('input', function() {
        const hasText = this.value.trim();
        generateButton3.disabled = !hasText;
        generateButton3.classList.toggle('btn-primary', hasText);
        generateButton3.classList.toggle('btn-secondary', !hasText);
    });

    generateButton3.addEventListener('click', function() {
        const instructions = instructionsTextarea3.value.trim();
        const numeroCnj = new URLSearchParams(window.location.search).get('numero_cnj');
        
        // Show loading on button
        const originalText = generateButton3.innerHTML;
        generateButton3.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gerando...';
        generateButton3.disabled = true;

        // Clear previous text
        generatedText3.value = '';
        ajustarAlturaTextarea(generatedText3);

        // Create SSE connection
        const eventSource = new EventSource(`${routes.streamFatos}?instrucoes=${encodeURIComponent(instructions)}&numero_cnj=${encodeURIComponent(numeroCnj)}`);

        eventSource.onmessage = function(event) {
            try {
                const data = JSON.parse(event.data);
                
                if (data.done) {
                    eventSource.close();
                    generateButton3.innerHTML = originalText;
                    generateButton3.disabled = false;
                    
                    // Update PDF with final text
                    pdfIframe.src = `${routes.getPdf}?quota=${encodeURIComponent(finalText1.value)}&partes=${encodeURIComponent(finalText2.value)}&fatos=${encodeURIComponent(finalText3.value)}`;
                    return;
                }
                
                if (data.text && typeof data.text === 'string') {
                    generatedText3.value += data.text;
                    generatedText3.scrollTop = generatedText3.scrollHeight;
                    ajustarAlturaTextarea(generatedText3);
                }
            } catch (e) {
                console.error('Erro ao processar mensagem:', e);
            }
        };

        eventSource.onerror = function(error) {
            console.error('Erro na conexão:', error);
            eventSource.close();
            generateButton3.innerHTML = originalText;
            generateButton3.disabled = false;
            alert('Erro ao gerar texto. Por favor, tente novamente.');
        };
    });

    approveButton3.addEventListener('click', function() {
        // Mark as approved
        const icon = this.querySelector('i');
        icon.classList.toggle('text-success');
        icon.classList.toggle('text-white');
        
        // Update PDF with approved text
        pdfIframe.src = `${routes.getPdf}?quota=${encodeURIComponent(finalText1.value)}&partes=${encodeURIComponent(finalText2.value)}&fatos=${encodeURIComponent(finalText3.value)}`;
    });
}); 