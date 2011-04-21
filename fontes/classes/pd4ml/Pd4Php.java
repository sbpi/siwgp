import java.awt.Dimension;
import java.awt.Insets;
import java.lang.reflect.Field;
import java.net.URL;

import org.zefer.pd4ml.PD4Constants;
import org.zefer.pd4ml.PD4ML;

public class Pd4Php {

	public static void main(String[] args) throws Exception {
		
		if ( args.length < 2 ) {
			System.out.println( "Uso: java -Xmx512m Pd4Php <url> <htmlWidth> <pageFormat> <pageOrientation> [TTFfontsDir]" );
		}
		
		Pd4Php converter = new Pd4Php();
		converter.generatePDF( args[0], args[1], args[2], args[3], args.length > 4 ? args[4] : null ); 
	}

	private void generatePDF(String inputUrl, String htmlWidth, String pageFormat, String pageOrientation, String fontsDir)
			throws Exception {

		PD4ML pd4ml = new PD4ML();
		pd4ml.setPageInsets(new Insets(10, 20, 10, 10)); 
		if (htmlWidth != null) {
			pd4ml.setHtmlWidth(Integer.parseInt(htmlWidth));
		}
		
		Class c = PD4Constants.class;
		Field f = c.getField( pageFormat );
		Dimension d = (Dimension)f.get( pd4ml );
		
		if (pageOrientation.equals("PORTRAIT")) pd4ml.setPageSize(d); else pd4ml.setPageSize( pd4ml.changePageOrientation( d ) ); 

		if ( fontsDir != null && fontsDir.length() > 0 ) {
			pd4ml.useTTF( fontsDir, true );
		}

//        String footerBody = "<table width=\"98%\" style=\"font-family: Helvetica, sans-serif; font-size: 8pt; font-style: italic; font-weight: normal;\">" +
//                    "<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>" +
//                    "<tr>" +
//                    "<td align=\"left\">$[title]</td>" +
//                    "<td align=\"right\">Page ${page} of ${total}</td>" +
//                    "</tr>" +
//                    "</table>";
//		PD4PageMark footer = new PD4PageMark();
//		footer.setAreaHeight( -1 ); // autocompute
//		footer.setHtmlTemplate( footerBody ); // autocompute
//		pd4ml.setPageFooter( footer );

//		pd4ml.enableDebugInfo();

		pd4ml.render(new URL(inputUrl), System.out);
	}
}
