<?php

namespace App\Services;

use FPDF;

class GeradorPdfFake
{
    public function gerar()
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Configurar suporte a UTF-8
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTitle('Processo Judicial', true);
        
        // Cabeçalho
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('PROCESSO JUDICIAL'), 0, 1, 'C');
        $pdf->Ln(10);

        // Informações da Capa
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('CAPA DO PROCESSO'), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 10, utf8_decode('Número do Processo:'), 0, 0);
        $pdf->Cell(0, 10, '0001234-12.2024.8.26.0000', 0, 1);
        
        $pdf->Cell(60, 10, utf8_decode('Data de Distribuição:'), 0, 0);
        $pdf->Cell(0, 10, '15/03/2024', 0, 1);
        
        $pdf->Cell(60, 10, utf8_decode('Foro:'), 0, 0);
        $pdf->Cell(0, 10, utf8_decode('Foro Central da Capital'), 0, 1);
        
        $pdf->Cell(60, 10, utf8_decode('Comarca:'), 0, 0);
        $pdf->Cell(0, 10, utf8_decode('São Paulo'), 0, 1);
        
        $pdf->Cell(60, 10, utf8_decode('Juiz:'), 0, 0);
        $pdf->Cell(0, 10, utf8_decode('Dr. João Silva'), 0, 1);
        
        $pdf->Ln(10);

        // Documentos
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('DOCUMENTOS'), 0, 1, 'C');
        $pdf->Ln(5);

        // Boletim de Ocorrência
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('BOLETIM DE OCORRÊNCIA'), 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, utf8_decode('No dia 10 de março de 2024, às 15:30 horas, na Rua das Flores, 123, foi registrado o presente boletim de ocorrência. O fato narrado pela vítima trata-se de furto qualificado, onde foram subtraídos bens no valor total de R$ 5.000,00. A vítima identificou o suspeito como sendo João da Silva, residente na Rua das Árvores, 456.'));
        $pdf->Ln(10);

        // Interrogatório
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('INTERROGATÓRIO'), 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, utf8_decode('Compareceu perante a autoridade policial o Sr. João da Silva, brasileiro, solteiro, comerciante, portador do RG nº 12.345.678-9, residente na Rua das Árvores, 456. O interrogado negou a prática do delito, alegando que no dia e horário do fato encontrava-se em sua residência, assistindo televisão. Declarou ainda que possui testemunhas que podem confirmar sua versão.'));
        $pdf->Ln(10);

        // Documentos Diversos
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('DOCUMENTOS DIVERSOS'), 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, utf8_decode('1. Cópia do RG da vítima\n2. Cópia do RG do acusado\n3. Comprovante de residência do acusado\n4. Laudo pericial do local do crime\n5. Fotos do local do crime'));

        return $pdf->Output('S');
    }
} 