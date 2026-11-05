<?php
$pages=get_posts(['post_type'=>'page','post_status'=>'draft','name'=>'simple-pdf']);
if(count($pages)!==1)throw new RuntimeException('Imported draft page missing');
$content=$pages[0]->post_content;
$blocks=parse_blocks($content);
if(count(array_filter($blocks,fn($b)=>$b['blockName']==='core/heading'))<5)throw new RuntimeException('Editable Gutenberg blocks missing');
$html=do_shortcode(do_blocks($content));
if(strpos($html,'data-smppb-select')!==false || strpos($html,'smppb-front-button')!==false)throw new RuntimeException('Example shortcode executed');
if(strpos(html_entity_decode($html),'[print_pdf_button template="123"')===false)throw new RuntimeException('Example not preserved');
if(strpos($html,'Download Simple PDF 4.4.2')===false)throw new RuntimeException('Download button missing');
echo "Standalone draft page and literal examples verified\n";
