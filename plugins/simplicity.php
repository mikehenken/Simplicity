<?php
# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

if(!isset($_GET['sidebar']))
{
    $plugin_page = "theme";
}
elseif(isset($_GET['sidebar']))
{
    $plugin_page = "pages";
}

# register plugin
register_plugin(
	$thisfile, 
	'Simplicity Settings', 	
	'.9', 		
	'Mike Henken',
	'http://michaelhenken.com', 
	'Simplicity Theme Settings And Info.',
	$plugin_page,
	'process_seo'  
);

add_action('theme-sidebar','createSideMenu',array($thisfile,'Simplicity Theme'));
add_action('pages-sidebar','createSideMenu',array($thisfile.'&sidebar','Edit Sidebar'));
define('SimplicityFile', GSDATAOTHERPATH  . 'simplicity.xml');

class GetSEOdata 
{	
	public function __construct() 
	{
		if (!file_exists(SimplicityFile)) 
		{
			$sidebar_data = '
				<h3>
					Useful Links</h3>
				<ul>
					<li>
						<a href="#">link 1</a></li>
					<li>
						<a href="#">link 2</a></li>
					<li>
						<a href="#">link 3</a></li>
					<li>
						<a href="#">link 4</a></li>
				</ul>
			';
			
			$footer_data = '<p style="text-align: left; ">Copyright &copy; |</p>';
			
			$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
			$xml->addChild('websitetitle', 'Website Title');
			$xml->addChild('websiteslogan', 'Website Slogan');
			$xml_sidebar = $xml->addChild('sidebar1');
			$xml_sidebar->addCData($sidebar_data);
			$xml_footer = $xml->addChild('footer');
			$xml_footer->addCData($footer_data);
			//Save XML File
			if(XMLsave($xml, SimplicityFile))
			{
					echo '<div class="updated">File Succesfully Written</div>';
			}
		}
	}
	
	public function SEOdata($get_keyphrase)
	{
            $keyphrase_file = getXML(SimplicityFile);
            $current_keyphrase = $get_keyphrase;
            $return_keyphrase = $keyphrase_file->$current_keyphrase;
            return $return_keyphrase;
	}
	
	public function ProcessSEO()
	{
            $old_data = new GetSEOdata;
            if(!isset($_POST['sidebar1']))
            {
               $sidebar_data = $old_data->SEOdata('sidebar1');
            }
            else
            {
                $sidebar_data = $_POST['sidebar1'];
            }
            
            if(!isset($_POST['footer']))
            {
               $footer_data = $old_data->SEOdata('footer');
            }
            else
            {
                $footer_data = $_POST['footer'];
            }
            
            if(!isset($_POST['websitetitle']))
            {
               $websitetitle_data = $old_data->SEOdata('websitetitle');
            }
            else
            {
                $websitetitle_data = $_POST['websitetitle'];
            }
            
            if(!isset($_POST['websiteslogan']))
            {
               $websiteslogan_data = $old_data->SEOdata('websiteslogan');
            }
            else
            {
                $websiteslogan_data = $_POST['websiteslogan'];
            }
            
            $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
            $xml->addChild('websitetitle', $websitetitle_data);
            $xml->addChild('websiteslogan', $websiteslogan_data);
            $xml_sidebar = $xml->addChild('sidebar1');
            $xml_sidebar->addCData($sidebar_data);
            $xml_footer = $xml->addChild('footer');
            $xml_footer->addCData($footer_data);
            //Save XML File
            if(XMLsave($xml, SimplicityFile))
            {
                    echo '<div class="updated">File Succesfully Written</div>';
            }
	}
        
        public function DownloadPlugin($id)
        {
								
					$plugins_data = new GetSEOdata;
					$pluginurl = $plugins_data->DownloadPlugins($id, 'file');
					$pluginfile = $plugins_data->DownloadPlugins($id, 'filename_id');
					
					$data = file_get_contents($pluginurl);
					$fp = fopen($pluginfile, "wb");
					fwrite($fp, $data);
					fclose($fp);
					
					function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true)
					{
					  if ($zip = zip_open($src_file))
					  {
						if ($zip)
						{
						  $splitter = ($create_zip_name_dir === true) ? "." : "/";
						  if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";

						  // Create the directories to the destination dir if they don't already exist
						  create_dirs($dest_dir);

						  // For every file in the zip-packet
						  while ($zip_entry = zip_read($zip))
						  {
							// Now we're going to create the directories in the destination directories

							// If the file is not in the root dir
							$pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
							if ($pos_last_slash !== false)
							{
							  // Create the directory where the zip-entry should be saved (with a "/" at the end)
							  create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
							}

							// Open the entry
							if (zip_entry_open($zip,$zip_entry,"r"))
							{

							  // The name of the file to save on the disk
							  $file_name = $dest_dir.zip_entry_name($zip_entry);

							  // Check if the files should be overwritten or not
							  if ($overwrite === true || $overwrite === false && !is_file($file_name))
							  {
								// Get the content of the zip entry
								$fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

								file_put_contents($file_name, $fstream );
								// Set the rights
								chmod($file_name, 0755);
							  }

							  // Close the entry
							  zip_entry_close($zip_entry);
							}
						  }
						  // Close the zip-file
						  zip_close($zip);
						}
					  }
					  else
					  {
						return false;
					  }

					  return true;
					}

					/**
					 * This function creates recursive directories if it doesn't already exist
					 *
					 * @param String  The path that should be created
					 *
					 * @return  void
					 */
					function create_dirs($path)
					{
					  if (!is_dir($path))
					  {
						$directory_path = "";
						$directories = explode("/",$path);
						array_pop($directories);

						foreach($directories as $directory)
						{
						  $directory_path .= $directory."/";
						  if (!is_dir($directory_path))
						  {
							mkdir($directory_path);
							chmod($directory_path, 0777);
						  }
						}
					  }
					}
					
					$pluginfile = $plugins_data->DownloadPlugins($id, 'filename_id');
					$pluginname = $plugins_data->DownloadPlugins($id, 'name');

					 /* Unzip the source_file in the destination dir
					 *
					 * @param   string      The path to the ZIP-file.
					 * @param   string      The path where the zipfile should be unpacked, if false the directory of the zip-file is used
					 * @param   boolean     Indicates if the files will be unpacked in a directory with the name of the zip-file (true) or not (false) (only if the destination directory is set to false!)
					 * @param   boolean     Overwrite existing files (true) or not (false)
					 *
					 * @return  boolean     Succesful or not
					 */

					// Extract C:/zipfiletest/zip-file.zip to C:/another_map/zipfiletest/ and doesn't overwrite existing files. NOTE: It doesn't create a map with the zip-file-name!
					$success = unzip($pluginfile, "../plugins/", true, true);
					if ($success){
					  print '<div class="updated">'.$pluginname.' was succesfully downloaded</div>';
					  
					if($pluginname == "News Manager")
					{
						mkdir("../data/other/news_manager", 0755);
						
						$settings_file = '../data/other/news_manager/settings.xml';
						$settings_file_data = file_get_contents('../plugins/simplicity/settings.xml');
						$settings_handle = fopen($settings_file, 'x+');
						fwrite($settings_handle, $settings_file_data);
						fclose($settings_handle);
						
						$blog_page_file = '../data/pages/blog.xml';
						$blog_page_data = file_get_contents('../plugins/simplicity/blog.xml');
						$blog_page_handle = fopen($blog_page_file, 'x+');
						fwrite($blog_page_handle, $blog_page_data);
						fclose($blog_page_handle);
					}
					}
					else{
					$pluginurl = $plugins_data->DownloadPlugins($id, 'file');
					$pluginfile = $plugins_data->DownloadPlugins($id, 'filename_id');
					  print "Error: DAMN! The Script Could Not Extract And CHMOD The Archive";
					}
	}
						
				public function DownloadPlugins($id, $get_field)
				{
					$my_plugin_id = $id; // replace this with yours

					$apiback = file_get_contents('http://get-simple.info/api/extend/?id='.$my_plugin_id);
					$response = json_decode($apiback);
					if ($response->status == 'successful') {
							// Successful api response sent back. 
							$get_field_data = $response->$get_field;
					}

            return $get_field_data;
        }
        
	public function ShowSettingsForm()
	{
            $currentKeyPhrase = 0;
            $keyphrase_data = new GetSEOdata;
                ?>
                    <form class="largeform" action="load.php?id=simplicity&submit_phrases" method="post" accept-charset="utf-8">
                        <h2>Simplicity Theme Setup</h2>
                        <p>
                                <label for="field-5">Website Title:</label>
                                <input type="text" class="text" name="websitetitle" value="<?php echo $keyphrase_data->SEOdata('websitetitle'); ?>" title="Displayed in your sites header" />
                        </p>
                        <p>
                                <label for="field-5">Website Slogan:</label>
                                <input type="text" class="text" name="websiteslogan" value="<?php echo $keyphrase_data->SEOdata('websiteslogan'); ?>" title="Displayed under Website Title in your sites header" />
                        </p>
                        <input type="submit" class="submit" name="post_phrases" value="submit" />
                    </form>
					
					<h2 style="margin-top:20px;">Auto Download GetSimple Plugins</h2>
					<h3 style="font-size:16px;margin-bottom:0px;">I18N Search - <a href="load.php?id=simplicity&download=search&download_id=82" style="font-size:14px;">Auto-Download This Plugin Now</a></h3>
					<strong>Author:</strong> <a href="http://mvlcek.bplaced.net/" style="font-size:12px;font-weight:normal" target="_blank">mvlcek</a>
					<p style="padding:0px;margin-bottom:10px;padding-top:10px;">
						<strong>Installing this plugin will insert a search box in your websites right column.</strong><br/><br/>
						If the plugin downloader does not work, you must download the plugin from here and follow the given directions to installing
					</p>
					<p>
						<strong>Description:</strong> This plugin allows you to search for tags (keywords) and words. It uses an automatically created index and orders the results by relevancy (number of occurences, title is more important than content, etc.).
					</p>
					
					<h3 style="font-size:16px;margin-bottom:0px;">News Manager - <a href="load.php?id=simplicity&download=search&download_id=43" style="font-size:14px;">Auto-Download This Plugin Now</a></h3>
					<strong>Author:</strong> <a href="http://get-simple.info/extend/a/roog" style="font-size:12px;font-weight:normal" target="_blank">roog</a>
					<p style="padding:0px;margin-bottom:10px;padding-top:10px;">
						<strong>Installing this plugin will add a blog to your website and a "Latest News" section in your sites right column</strong><br/><br/>
						If the plugin downloader does not work, you must download the plugin from here and follow the given directions to installing
					</p>
					<p>
						<strong>Description:</strong> A news/blog plugin to manage your posts.
					</p>
                <?php
	}
        
        public function ShowSidebarForm()
	{
            $currentKeyPhrase = 0;
            $keyphrase_data = new GetSEOdata;
            
        if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {	$EDLANG = 'en'; }
	if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else {	$EDTOOL = 'basic'; }
	if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {	$EDOPTIONS = ''; }
	if ($EDTOOL == 'advanced') {
	$toolbar = "
		    ['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
	    '/',
	    ['Styles','Format','Font','FontSize']
	";
	} elseif ($EDTOOL == 'basic') {
	$toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
	} else {
	$toolbar = GSEDITORTOOL;
	}
	?>
                    <form class="largeform" action="load.php?id=simplicity&sidebar&submit_phrases" method="post" accept-charset="utf-8">
                        <h2>Edit Sidebar</h2>
                        <p style="width:648px !important;">
                            <textarea id="sidebar-1" name="sidebar1" style=""><?php echo $keyphrase_data->SEOdata('sidebar1'); ?></textarea>       
                	</p>
                
                        <h2>Edit Footer</h2>
                        <p style="width:648px !important;">
                            <textarea id="footer" name="footer" style=""><?php echo $keyphrase_data->SEOdata('footer'); ?></textarea>       
                        </p>
                <script type="text/javascript" src="../admin/template/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
		  // missing border around text area, too much padding on left side, ...
		  $(function() {
		    CKEDITOR.replace( 'sidebar-1', {
			        skin : 'getsimple',
			        forcePasteAsPlainText : false,
			        language : '<?php echo $EDLANG; ?>',
			        defaultLanguage : '<?php echo $EDLANG; ?>',
			        entities : true,
			        uiColor : '#FFFFFF',
					    height: '200px',
					    baseHref : '<?php echo $SITEURL; ?>',
			        toolbar : [ <?php echo $toolbar; ?> ]
					    <?php echo $EDOPTIONS; ?>
		    })
		  });
		</script>
                <script type="text/javascript">
		  // missing border around text area, too much padding on left side, ...
		  $(function() {
		    CKEDITOR.replace( 'footer', {
			        skin : 'getsimple',
			        forcePasteAsPlainText : false,
			        language : '<?php echo $EDLANG; ?>',
			        defaultLanguage : '<?php echo $EDLANG; ?>',
			        entities : true,
			        uiColor : '#FFFFFF',
					    height: '200px',
					    baseHref : '<?php echo $SITEURL; ?>',
			        toolbar : [ <?php echo $toolbar; ?> ]
					    <?php echo $EDOPTIONS; ?>
		    })
		  });
		</script>

                        <input type="submit" class="submit" name="post_phrases" value="submit" />
                    </form>
                <?php
	}
}

function process_seo()
{
    if(isset($_POST['post_phrases']))
    {
        $Submit_SEO = new GetSEOdata;
        $Submit_SEO->ProcessSEO();
    }
    
    if(isset($_GET['download']))
    {
        $Submit_SEO = new GetSEOdata;
        $Submit_SEO->DownloadPlugin($_GET['download_id']);
    }
    
    $Submit_SEO = new GetSEOdata;
    if(!isset($_GET['sidebar']))
    {
        $Submit_SEO->ShowSettingsForm();
    }
    
    elseif(isset($_GET['sidebar']))
    {
        $Submit_SEO->ShowSidebarForm();
    }
}

function return_simplicity($keyphraseN)
{
    $keyphrase_data = new GetSEOdata;
    echo $keyphrase_data->SEOdata($keyphraseN);
	
}

?>
