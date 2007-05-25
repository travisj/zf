<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/** Zend_Pdf */
require_once 'Zend/Pdf.php';

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_DrawingTest extends PHPUnit_Framework_TestCase
{
    public function testDrawing()
    {
        $pdf = new Zend_Pdf();

        // Add new page generated by Zend_Pdf object (page is attached to the specified the document)
        $pdf->pages[] = ($page1 = $pdf->newPage('A4'));

        // Add new page generated by Zend_Pdf_Page object (page is not attached to the document)
        $pdf->pages[] = ($page2 = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE));

        // Create new font
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);

        // Apply font and draw text
        $page1->setFont($font, 36);
        $page1->setFillColor(Zend_Pdf_Color_Html::color('#9999cc'));
        $page1->drawText('Helvetica 36 text string', 60, 500);

        // Use font object for another page
        $page2->setFont($font, 24);
        $page2->drawText('Helvetica 24 text string', 60, 500);

        // Use another font
        $page2->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES), 32);
        $page2->drawText('Times-Roman 32 text string', 60, 450);

        // Draw rectangle
        $page2->setFillColor(new Zend_Pdf_Color_GrayScale(0.8));
        $page2->setLineColor(new Zend_Pdf_Color_GrayScale(0.2));
        $page2->setLineDashingPattern(array(3, 2, 3, 4), 1.6);
        $page2->drawRectangle(60, 400, 400, 350);

        // Draw circle
        $page2->setLineDashingPattern(Zend_Pdf_Page::LINE_DASHING_SOLID);
        $page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 0));
        $page2->drawCircle(85, 375, 25);

        // Draw sectors
        $page2->drawCircle(200, 375, 25, 2*M_PI/3, -M_PI/6);
        $page2->setFillColor(new Zend_Pdf_Color_Cmyk(1, 0, 0, 0));
        $page2->drawCircle(200, 375, 25, M_PI/6, 2*M_PI/3);
        $page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 0));
        $page2->drawCircle(200, 375, 25, -M_PI/6, M_PI/6);

        // Draw ellipse
        $page2->setFillColor(new Zend_Pdf_Color_Html('Red'));
        $page2->drawEllipse(250, 400, 400, 350);
        $page2->setFillColor(new Zend_Pdf_Color_Cmyk(1, 0, 0, 0));
        $page2->drawEllipse(250, 400, 400, 350, M_PI/6, 2*M_PI/3);
        $page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 0));
        $page2->drawEllipse(250, 400, 400, 350, -M_PI/6, M_PI/6);

        // Draw and fill polygon
        $page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 1));
        $x = array();
        $y = array();
        for ($count = 0; $count < 8; $count++) {
            $x[] = 140 + 25*cos(3*M_PI_4*$count);
            $y[] = 375 + 25*sin(3*M_PI_4*$count);
        }
        $page2->drawPolygon($x, $y,
                            Zend_Pdf_Page::SHAPE_DRAW_FILL_AND_STROKE,
                            Zend_Pdf_Page::FILL_METHOD_EVEN_ODD);

        // Draw line
        $page2->setLineWidth(0.5);
        $page2->drawLine(60, 375, 400, 375);

        $pdf->save(dirname(__FILE__) . '/_files/output.pdf');
        unset($pdf);

        $pdf1 = Zend_Pdf::load(dirname(__FILE__) . '/_files/output.pdf');
        $this->assertTrue($pdf1 instanceof Zend_Pdf);
        unset($pdf1);

        unlink(dirname(__FILE__) . '/_files/output.pdf');
    }

    public function testImageDrawing()
    {
        $pdf = new Zend_Pdf();

        // Add new page generated by Zend_Pdf object (page is attached to the specified the document)
        $pdf->pages[] = ($page = $pdf->newPage('A4'));


        $stampImagePNG = Zend_Pdf_Image::imageWithPath(dirname(__FILE__) . '/_files/stamp.png');
        $this->assertTrue($stampImagePNG instanceof Zend_Pdf_Resource_Image);

        $page->saveGS();
        $page->clipCircle(250, 500, 50);
        $page->drawImage($stampImagePNG, 200, 450, 300, 550);
        $page->restoreGS();


        $stampImageTIFF = Zend_Pdf_Image::imageWithPath(dirname(__FILE__) . '/_files/stamp.tif');
        $this->assertTrue($stampImageTIFF instanceof Zend_Pdf_Resource_Image);

        $page->saveGS();
        $page->clipCircle(325, 500, 50);
        $page->drawImage($stampImagePNG, 275, 450, 375, 550);
        $page->restoreGS();

        if (function_exists('gd_info')) {
            $info = gd_info();
            $jpegSupported = $info['JPG Support'];
        } else {
            $jpegSupported = false;
        }
        if ($jpegSupported) {
            $stampImageJPG = Zend_Pdf_Image::imageWithPath(dirname(__FILE__) . '/_files/stamp.jpg');

            $this->assertTrue($stampImageJPG instanceof Zend_Pdf_Resource_Image);

            $page->saveGS();
            $page->clipCircle(287.5, 440, 50);
            $page->drawImage($stampImageJPG, 237.5, 390, 337.5, 490);
            $page->restoreGS();

            $page->saveGS();
            $page->clipCircle(250, 500, 50);
            $page->clipCircle(287.5, 440, 50);
            $page->drawImage($stampImagePNG, 200, 450, 300, 550);
            $page->restoreGS();
        }

        $pdf->save(dirname(__FILE__) . '/_files/output.pdf');
        unset($pdf);

        $pdf1 = Zend_Pdf::load(dirname(__FILE__) . '/_files/output.pdf');
        $this->assertTrue($pdf1 instanceof Zend_Pdf);
        unset($pdf1);

        unlink(dirname(__FILE__) . '/_files/output.pdf');
    }

    public function testFontDrawing()
    {
        $pdf = new Zend_Pdf();

        // Add new page generated by Zend_Pdf object (page is attached to the specified the document)
        $pdf->pages[] = ($page = $pdf->newPage('A4'));


        $stampImagePNG = Zend_Pdf_Image::imageWithPath(dirname(__FILE__) . '/_files/stamp.png');
        $this->assertTrue($stampImagePNG instanceof Zend_Pdf_Resource_Image);

        $page->saveGS();
        $page->clipCircle(250, 500, 50);
        $page->drawImage($stampImagePNG, 200, 450, 300, 550);
        $page->restoreGS();


        $stampImageTIFF = Zend_Pdf_Image::imageWithPath(dirname(__FILE__) . '/_files/stamp.tif');
        $this->assertTrue($stampImageTIFF instanceof Zend_Pdf_Resource_Image);

        $page->saveGS();
        $page->clipCircle(325, 500, 50);
        $page->drawImage($stampImagePNG, 275, 450, 375, 550);
        $page->restoreGS();

        if (function_exists('gd_info')) {
            $info = gd_info();
            $jpegSupported = $info['JPG Support'];
        } else {
            $jpegSupported = false;
        }
        if ($jpegSupported) {
            $stampImageJPG = Zend_Pdf_Image::imageWithPath(dirname(__FILE__) . '/_files/stamp.jpg');

            $this->assertTrue($stampImageJPG instanceof Zend_Pdf_Resource_Image);

            $page->saveGS();
            $page->clipCircle(287.5, 440, 50);
            $page->drawImage($stampImageJPG, 237.5, 390, 337.5, 490);
            $page->restoreGS();

            $page->saveGS();
            $page->clipCircle(250, 500, 50);
            $page->clipCircle(287.5, 440, 50);
            $page->drawImage($stampImagePNG, 200, 450, 300, 550);
            $page->restoreGS();
        }

        $pdf->save(dirname(__FILE__) . '/_files/output.pdf');
        unset($pdf);

        $pdf1 = Zend_Pdf::load(dirname(__FILE__) . '/_files/output.pdf');
        $this->assertTrue($pdf1 instanceof Zend_Pdf);
        unset($pdf1);

        unlink(dirname(__FILE__) . '/_files/output.pdf');
    }
}
