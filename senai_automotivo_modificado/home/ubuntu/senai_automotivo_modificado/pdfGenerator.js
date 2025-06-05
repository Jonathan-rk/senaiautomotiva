// pdfGenerator.js

// Ensure jsPDF and html2canvas are loaded before this script in index.html

// Destructure jsPDF from the global window object
const { jsPDF } = window.jspdf;

async function generatePdfForFicha(carId) {
    const car = carData.find(c => c.id === carId);
    if (!car) {
        console.error("Carro não encontrado para gerar PDF:", carId);
        alert("Erro ao gerar PDF: Ficha técnica não encontrada.");
        return;
    }

    // Create a temporary, off-screen container for rendering
    const tempContainer = document.createElement("div");
    tempContainer.style.position = "absolute";
    tempContainer.style.left = "-9999px"; // Position off-screen
    tempContainer.style.width = "800px"; // A reasonable width for PDF content
    tempContainer.style.padding = "20px";
    tempContainer.style.backgroundColor = "white"; // Ensure white background
    tempContainer.style.fontFamily = "sans-serif"; // Basic font
    tempContainer.innerHTML = generateFichaHtml(car); // Reuse the HTML generation function from script.js
    document.body.appendChild(tempContainer);

    // Add a small delay to ensure rendering and image loading (if any)
    await new Promise(resolve => setTimeout(resolve, 300));

    try {
        // Use html2canvas to capture the container
        const canvas = await html2canvas(tempContainer, {
            scale: 2, // Increase scale for better resolution
            useCORS: true, // If images are from external sources
            logging: false // Disable logging for cleaner console
        });

        // Clean up the temporary container
        document.body.removeChild(tempContainer);

        const imgData = canvas.toDataURL("image/png");
        const pdf = new jsPDF({
            orientation: "portrait",
            unit: "pt", // Use points for better control over dimensions
            format: "a4"
        });

        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();
        const margin = 40; // Page margin in points
        const contentWidth = pdfWidth - (margin * 2);

        // Calculate image dimensions to fit the page width
        const imgProps = pdf.getImageProperties(imgData);
        const imgHeight = (imgProps.height * contentWidth) / imgProps.width;

        let heightLeft = imgHeight;
        let position = margin; // Start position for the first page

        // Add the first chunk of the image
        pdf.addImage(imgData, "PNG", margin, position, contentWidth, imgHeight);
        heightLeft -= (pdfHeight - (margin * 2)); // Subtract the height of the visible area

        // Add new pages if the content is taller than one page
        while (heightLeft > 0) {
            position = heightLeft - imgHeight + margin; // Calculate the position for the next chunk
            pdf.addPage();
            pdf.addImage(imgData, "PNG", margin, position, contentWidth, imgHeight);
            heightLeft -= (pdfHeight - margin * 2);
        }

        // Sanitize filename
        const filename = `Ficha_Tecnica_${car.marca}_${car.modelo}_${car.ano}.pdf`.replace(/[^a-z0-9_.-]/gi, '_');

        // Save the PDF
        pdf.save(filename);
        console.log(`PDF gerado: ${filename}`);

    } catch (error) {
        console.error("Erro ao gerar PDF com html2canvas:", error);
        alert("Ocorreu um erro ao gerar o PDF. Verifique o console para mais detalhes.");
        // Clean up the temporary container in case of error
        if (document.body.contains(tempContainer)) {
            document.body.removeChild(tempContainer);
        }
    }
}

// Make sure generateFichaHtml is accessible globally or passed correctly
// If script.js defines generateFichaHtml, ensure pdfGenerator.js is loaded AFTER script.js
// OR pass generateFichaHtml as an argument or make it global (less ideal).
// For simplicity here, we assume generateFichaHtml is available globally from script.js.

