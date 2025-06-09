<?php
require_once 'vendor/autoload.php'; // Nécessite l'installation de PhpSpreadsheet et TCPDF

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;

class ExportRapport {
    /**
     * Exporte les données en format Excel
     */
    public static function exporterExcel($donnees, $colonnes, $nomFichier) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // En-têtes
        $col = 'A';
        foreach ($colonnes as $colonne) {
            $sheet->setCellValue($col . '1', $colonne);
            $col++;
        }
        
        // Données
        $row = 2;
        foreach ($donnees as $ligne) {
            $col = 'A';
            foreach ($ligne as $valeur) {
                $sheet->setCellValue($col . $row, $valeur);
                $col++;
            }
            $row++;
        }
        
        // Style
        $sheet->getStyle('A1:' . $col . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $col . ($row-1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Enregistrer le fichier
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nomFichier . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
    
    /**
     * Exporte les données en format PDF
     */
    public static function exporterPDF($donnees, $colonnes, $nomFichier, $titre = '') {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configuration du document
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Système de Gestion de Stock');
        $pdf->SetTitle($titre);
        
        // En-tête et pied de page
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Ajouter une page
        $pdf->AddPage();
        
        // Titre
        if ($titre) {
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 15, $titre, 0, 1, 'C');
            $pdf->Ln(10);
        }
        
        // En-têtes de colonnes
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(240, 240, 240);
        $colWidth = 190 / count($colonnes);
        foreach ($colonnes as $colonne) {
            $pdf->Cell($colWidth, 7, $colonne, 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Données
        $pdf->SetFont('helvetica', '', 10);
        foreach ($donnees as $ligne) {
            foreach ($ligne as $valeur) {
                $pdf->Cell($colWidth, 6, $valeur, 1, 0, 'C');
            }
            $pdf->Ln();
        }
        
        // Enregistrer le fichier
        $pdf->Output($nomFichier . '.pdf', 'D');
    }
    
    /**
     * Génère un rapport de stock faible
     */
    public static function rapportStockFaible($db) {
        $query = "SELECT p.nom, p.quantite, p.quantite_min, c.nom as categorie, f.nom as fournisseur
                 FROM produits p
                 LEFT JOIN categories c ON p.categorie_id = c.id
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                 WHERE p.quantite <= p.quantite_min
                 ORDER BY p.quantite ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Génère un rapport des mouvements de stock
     */
    public static function rapportMouvementsStock($db, $dateDebut = null, $dateFin = null) {
        $query = "SELECT m.date_mouvement, p.nom as produit, m.type_mouvement, 
                        m.quantite, u.nom_complet as utilisateur
                 FROM mouvements_stock m
                 LEFT JOIN produits p ON m.produit_id = p.id
                 LEFT JOIN utilisateurs u ON m.utilisateur_id = u.id
                 WHERE 1=1";
        
        if ($dateDebut) {
            $query .= " AND m.date_mouvement >= :date_debut";
        }
        if ($dateFin) {
            $query .= " AND m.date_mouvement <= :date_fin";
        }
        
        $query .= " ORDER BY m.date_mouvement DESC";
        
        $stmt = $db->prepare($query);
        if ($dateDebut) {
            $stmt->bindParam(':date_debut', $dateDebut);
        }
        if ($dateFin) {
            $stmt->bindParam(':date_fin', $dateFin);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 