<?php
// Executed by wp eval-file after plugin activation, using real WordPress functions.
$GLOBALS['smppb_test_count']=0;
function smppb_test($ok,$label) { if (!$ok) throw new RuntimeException($label); $GLOBALS['smppb_test_count']++; }
smppb_test(function_exists('smppb_pdf_filename'),'Plugin loaded');
update_option('smppb_pdf_output','download');
smppb_test(smppb_output_mode()==='download','Download default');
smppb_test(smppb_output_mode('download',true)==='inline','Save & Test remains inline');
smppb_test(smppb_output_mode('inline')==='inline','Inline override');
$a=smppb_sanitize_appearance(['background'=>'bad','text'=>'#abc','radius'=>100,'icon'=>'unknown','size'=>'unknown']);
smppb_test($a['background']==='#1e5fa8','Invalid colour fallback');
smppb_test($a['text']==='#aabbcc','Short hex expanded');
smppb_test($a['radius']===40,'Radius limit');
smppb_test($a['icon']==='none' && $a['size']==='theme','Unknown options rejected');
smppb_test(smppb_filename_pattern('{unknown}')==='{title}','Unknown filename placeholder rejected');
update_option('smppb_filename_pattern','{title}-{id}.pdf');
$post=(object)['post_title'=>'Test document','ID'=>123];
$template=(object)['post_title'=>'Default'];
smppb_test(smppb_pdf_filename($post,$template)==='test-document-123.pdf','Filename expansion, single extension');
$header=smppb_content_disposition("test\r\nInjected: yes.pdf",'download');
smppb_test(strpos($header,'attachment;')===0 && !preg_match('/[\r\n]/',$header),'Safe download header');
smppb_test(strpos(smppb_content_disposition('test.pdf','inline'),'inline;')===0,'Inline header');
require_once SMPPB_PATH.'mpdf/autoload.php';
$pdf=new \Mpdf\Mpdf(['tempDir'=>sys_get_temp_dir().'/simplepdf-ci']);
$pdf->WriteHTML('<h1>SimplePDF GitHub test</h1><p>WordPress en mPDF werken samen.</p>');
$bytes=$pdf->Output('','S');
smppb_test(strncmp($bytes,'%PDF-',5)===0 && strlen($bytes)>1000,'Real PDF output');
file_put_contents(getenv('GITHUB_WORKSPACE').'/test-output.pdf',$bytes);
if ($GLOBALS['smppb_test_count'] !== 13) throw new RuntimeException('Unexpected test count');
echo $GLOBALS['smppb_test_count']." WordPress/PDF checks passed\n";
