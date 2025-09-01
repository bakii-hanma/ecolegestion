function generatePDF(studentData, gradesData, cumulativeScore, cumulativePercentage, term, academicYear, classInfo) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Configuration
    const pageWidth = doc.internal.pageSize.width;
    const margin = 15;
    
    let yPosition = margin;
    
    // En-tête
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text(`BULLETIN - ${term || "3ème TRIMESTRE"}`, pageWidth / 2, yPosition, { align: 'center' });
    yPosition += 10;
    
    // Informations de l'école
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text('Ministère de l\'Education Nationale', margin, yPosition);
    doc.text(`Année Scolaire : ${academicYear || "2024-2025"}`, pageWidth - margin, yPosition, { align: 'right' });
    yPosition += 8;
    
    // Informations de l'élève
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('INFORMATIONS DE L\'ÉLÈVE', margin, yPosition);
    yPosition += 8;
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text(`Nom et Prénom : ${studentData.first_name} ${studentData.last_name}`, margin, yPosition);
    yPosition += 6;
    
    doc.text(`Classe : ${classInfo.name || 'N/A'}`, margin, yPosition);
    doc.text(`Niveau : ${classInfo.level || 'N/A'}`, margin + 60, yPosition);
    yPosition += 6;
    
    doc.text(`Effectif de la classe : ${classInfo.students_count || 'N/A'} élèves`, margin, yPosition);
    yPosition += 10;
    
    // Tableau des notes
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('RELEVÉ DE NOTES', margin, yPosition);
    yPosition += 8;
    
    // En-têtes du tableau
    const tableHeaders = [
        'DISCIPLINES',
        'MOYENNE',
        'COEF',
        'NOTE X COEF',
        'RANG',
        'ABSENCES',
        'APPRÉCIATION',
        'PROFESSEUR'
    ];
    
    // Données du tableau
    const tableData = gradesData || [];
    
    // Ajouter la ligne des totaux si des notes existent
    if (gradesData && gradesData.length > 0) {
        const totalCoeff = gradesData.length;
        const totalNoteCoeff = gradesData.reduce((sum, row) => {
            const noteValue = parseFloat(row[3]) || 0;
            return sum + noteValue;
        }, 0);
        
        tableData.push([
            'TOTAUX',
            `${cumulativeScore}/20`,
            totalCoeff,
            totalNoteCoeff.toFixed(2),
            '--',
            '0h00',
            cumulativePercentage >= 50 ? "Admis" : "Non admis",
            ''
        ]);
    }
    
    // Générer le tableau
    doc.autoTable({
        startY: yPosition,
        head: [tableHeaders],
        body: tableData,
        theme: 'grid',
        styles: {
            fontSize: 8,
            cellPadding: 2
        },
        headStyles: {
            fillColor: [200, 200, 200],
            textColor: [0, 0, 0],
            fontStyle: 'bold'
        },
        columnStyles: {
            0: { cellWidth: 35 }, // DISCIPLINES
            1: { cellWidth: 20 }, // MOYENNE
            2: { cellWidth: 15 }, // COEF
            3: { cellWidth: 25 }, // NOTE X COEF
            4: { cellWidth: 15 }, // RANG
            5: { cellWidth: 20 }, // ABSENCES
            6: { cellWidth: 25 }, // APPRÉCIATION
            7: { cellWidth: 30 }  // PROFESSEUR
        }
    });
    
    yPosition = doc.lastAutoTable.finalY + 10;
    
    // Résumé et décision
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('RÉSUMÉ', margin, yPosition);
    yPosition += 8;
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text(`Moyenne générale : ${cumulativeScore}/20`, margin, yPosition);
    yPosition += 6;
    
    let appreciation = 'Insuffisant';
    if (cumulativePercentage >= 80) appreciation = 'Excellent';
    else if (cumulativePercentage >= 70) appreciation = 'Très bien';
    else if (cumulativePercentage >= 60) appreciation = 'Bien';
    else if (cumulativePercentage >= 50) appreciation = 'Assez bien';
    else if (cumulativePercentage >= 40) appreciation = 'Passable';
    
    doc.text(`Appréciation générale : ${appreciation}`, margin, yPosition);
    yPosition += 6;
    
    doc.text(`Décision : ${cumulativePercentage >= 50 ? "Admis(e)" : "Non admis(e)"}`, margin, yPosition);
    yPosition += 10;
    
    // Signature
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text('Signature du professeur principal :', margin, yPosition);
    doc.text('_________________', margin + 80, yPosition);
    yPosition += 8;
    
    const today = new Date().toLocaleDateString('fr-FR');
    doc.text(`Date : ${today}`, margin, yPosition);
    doc.text('Cachet :', margin + 80, yPosition);
    doc.text('_________________', margin + 100, yPosition);
    
    // Sauvegarder le PDF
    const fileName = `bulletin_${studentData.first_name}_${studentData.last_name}_${term || 'trimestre'}.pdf`;
    doc.save(fileName);
}
