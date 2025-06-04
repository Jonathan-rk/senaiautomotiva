// pdfGenerator.js - Implementação de geração de PDF para fichas técnicas

// Carregar biblioteca jsPDF via CDN
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se jsPDF já está carregado
    if (typeof jsPDF === 'undefined') {
        // Carregar jsPDF
        const jsPDFScript = document.createElement('script');
        jsPDFScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        document.head.appendChild(jsPDFScript);
        
        // Carregar html2canvas
        const html2canvasScript = document.createElement('script');
        html2canvasScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
        document.head.appendChild(html2canvasScript);
    }
});

// Função para gerar PDF a partir do ID do veículo
function gerarPDF(carId) {
    // Verificar se as bibliotecas estão carregadas
    if (typeof jsPDF === 'undefined' || typeof html2canvas === 'undefined') {
        alert('Carregando bibliotecas necessárias. Por favor, tente novamente em alguns segundos.');
        return;
    }
    
    // Obter dados do veículo
    const veiculo = obterVeiculoPorId(carId);
    
    if (!veiculo) {
        alert('Veículo não encontrado.');
        return;
    }
    
    // Criar elemento temporário para renderizar o conteúdo do PDF
    const tempElement = document.createElement('div');
    tempElement.className = 'pdf-container';
    tempElement.style.width = '210mm';
    tempElement.style.padding = '10mm';
    tempElement.style.backgroundColor = 'white';
    tempElement.style.position = 'absolute';
    tempElement.style.left = '-9999px';
    tempElement.style.top = '0';
    tempElement.style.fontFamily = 'Arial, sans-serif';
    
    // Adicionar conteúdo ao elemento temporário
    tempElement.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: #0A3871; font-size: 24px; margin-bottom: 5px;">${veiculo.marca} ${veiculo.modelo}</h1>
            <p style="color: #666; font-size: 16px;">${veiculo.versao || ''} - ${veiculo.ano}</p>
        </div>
        
        <div style="margin-bottom: 20px; text-align: center;">
            <img src="${veiculo.imagem || 'images/placeholder.png'}" style="max-width: 100%; max-height: 200px; object-fit: contain;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <h2 style="color: #0A3871; font-size: 18px; border-bottom: 2px solid #0A3871; padding-bottom: 5px;">Informações Básicas</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Marca</span>
                    <span style="font-weight: bold;">${veiculo.marca || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Modelo</span>
                    <span style="font-weight: bold;">${veiculo.modelo || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Ano</span>
                    <span style="font-weight: bold;">${veiculo.ano || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Versão</span>
                    <span style="font-weight: bold;">${veiculo.versao || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Código do Motor</span>
                    <span style="font-weight: bold;">${veiculo.codigo_motor || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Combustível</span>
                    <span style="font-weight: bold;">${veiculo.combustivel || '-'}</span>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h2 style="color: #0A3871; font-size: 18px; border-bottom: 2px solid #0A3871; padding-bottom: 5px;">Motorização</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Tipo de Motor</span>
                    <span style="font-weight: bold;">${veiculo.tipo_motor || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Cilindrada (cm³)</span>
                    <span style="font-weight: bold;">${veiculo.cilindrada_cm3 || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Potência Máxima (cv)</span>
                    <span style="font-weight: bold;">${veiculo.potencia_cv || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Torque Máximo (kgfm)</span>
                    <span style="font-weight: bold;">${veiculo.torque_kgfm || '-'}</span>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h2 style="color: #0A3871; font-size: 18px; border-bottom: 2px solid #0A3871; padding-bottom: 5px;">Transmissão</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Tipo de Câmbio</span>
                    <span style="font-weight: bold;">${veiculo.cambio || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Número de Marchas</span>
                    <span style="font-weight: bold;">${veiculo.marchas || '-'}</span>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h2 style="color: #0A3871; font-size: 18px; border-bottom: 2px solid #0A3871; padding-bottom: 5px;">Desempenho e Consumo</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Velocidade Máxima (km/h)</span>
                    <span style="font-weight: bold;">${veiculo.velocidade_max_kmh || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Aceleração 0-100 km/h (s)</span>
                    <span style="font-weight: bold;">${veiculo.aceleracao_0_100_s || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Consumo Urbano (km/L)</span>
                    <span style="font-weight: bold;">${veiculo.consumo_urbano_kmL || '-'}</span>
                </div>
                <div style="background-color: #f8f9fa; padding: 8px; border-left: 3px solid #0A3871;">
                    <span style="display: block; color: #666; font-size: 12px;">Consumo Rodoviário (km/L)</span>
                    <span style="font-weight: bold;">${veiculo.consumo_rodoviario_kmL || '-'}</span>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
            <p>SENAI Automotivo - Ficha Técnica</p>
            <p>Documento gerado em ${new Date().toLocaleDateString('pt-BR')}</p>
        </div>
    `;
    
    document.body.appendChild(tempElement);
    
    // Usar html2canvas para renderizar o elemento como imagem
    html2canvas(tempElement, {
        scale: 2, // Melhor qualidade
        useCORS: true, // Permitir imagens de outros domínios
        logging: false
    }).then(canvas => {
        // Criar PDF
        const pdf = new jspdf.jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
        
        // Adicionar a imagem do canvas ao PDF
        const imgData = canvas.toDataURL('image/jpeg', 1.0);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();
        const canvasRatio = canvas.height / canvas.width;
        const pdfRatio = pdfHeight / pdfWidth;
        
        let finalWidth, finalHeight;
        
        if (canvasRatio > pdfRatio) {
            // Canvas é mais alto proporcionalmente
            finalHeight = pdfHeight;
            finalWidth = finalHeight / canvasRatio;
        } else {
            // Canvas é mais largo proporcionalmente
            finalWidth = pdfWidth;
            finalHeight = finalWidth * canvasRatio;
        }
        
        // Centralizar na página
        const xOffset = (pdfWidth - finalWidth) / 2;
        const yOffset = 0;
        
        pdf.addImage(imgData, 'JPEG', xOffset, yOffset, finalWidth, finalHeight);
        
        // Salvar o PDF
        pdf.save(`${veiculo.marca}_${veiculo.modelo}_${veiculo.ano}.pdf`);
        
        // Remover o elemento temporário
        document.body.removeChild(tempElement);
    });
}

// Função para gerar PDF a partir da página de detalhes
function gerarPDFDaPaginaDetalhes() {
    const id = obterIdVeiculoDaURL();
    if (id) {
        gerarPDF(id);
    } else {
        alert('ID do veículo não encontrado.');
    }
}

// Obter o ID do veículo da URL (função auxiliar)
function obterIdVeiculoDaURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
}
