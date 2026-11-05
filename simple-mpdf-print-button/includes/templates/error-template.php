<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error generating PDF</title>
    <style>
        body {
            font-family: verdana, sans-serif;
            background: #f4f4f4;
            padding: 40px;
			font-size: 12px;
        }
        .box {
            max-width: 800px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0c2a40;
        }
		 h2 {
            color: #2a87c1;
        }
		hr{
			border: 0;
			height: 1px;
			background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
		}
        p, {
            font-size: 12px;
        }
        code {
            background: #eee;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
		a {
			color: #0c2a40;;
			font-weight: 600;
		}
    </style>
</head>
<body>
    <div class="box">
        <h1><img src="<?php echo plugins_url( 'img/crying pug.png', __FILE__ ); ?>" alt="CodePug" height="80">OH NO! Error generating PDF</h1>

        <?php
        $error_code = $_GET['error_code'] ?? 'unknown';
        $template_id = isset($_GET['template_id']) ? intval($_GET['template_id']) : null;
        $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;

echo "<hr>";
echo "<h2>Error Information</h2>";
echo "<table style='border-collapse: collapse; width: 100%; max-width: 800px; margin-top: 1em;'>";
echo "<tr style='background-color: #f2f2f2;'>
        <th style=' padding: 8px; text-align: left;'>Field</th>
        <th style=' padding: 8px; text-align: left;'>Details</th>
      </tr>";

// Foutcode
echo "<tr>
        <td style=' padding: 8px;'>Error Code</td>
        <td style=' padding: 8px;'><code>$error_code</code></td>
      </tr>";

switch ($error_code) {
    case '01':
        echo "<tr>
                <td style=' padding: 8px;'>Issue</td>
                <td style=' padding: 8px;'>The provided <code>post_id</code> does not exist or is invalid.</td>
              </tr>";
        if ($post_id) {
            echo "<tr>
                    <td style=' padding: 8px;'>Post ID</td>
                    <td style=' padding: 8px;'>$post_id</td>
                  </tr>";
        }
        echo "<tr>
                <td style=' padding: 8px;'>Hint</td>
                <td style=' padding: 8px;'>Check if your shortcode or URL refers to a valid post.</td>
              </tr>";
        break;

    case '02':
        echo "<tr>
                <td style=' padding: 8px;'>Issue</td>
                <td style=' padding: 8px;'>The provided <code>template_id</code> is missing or not of type <code>pdf_template</code>.</td>
              </tr>";
        if ($template_id) {
            echo "<tr>
                    <td style=' padding: 8px;'>Template ID</td>
                    <td style=' padding: 8px;'>$template_id</td>
                  </tr>";
        }
        echo "<tr>
                <td style=' padding: 8px;'>Hint</td>
                <td style=' padding: 8px;'>Ensure your shortcode points to a valid PDF template, e.g. <code>[print_pdf_button template=\"123\"]</code>.<br>You can find template IDs in the admin panel under <strong>All Templates</strong>.</td>
              </tr>";
        break;

    case '03':
        echo "<tr>
                <td style=' padding: 8px;'>Issue</td>
                <td style=' padding: 8px;'>No template code was found for the given <code>template_id</code>.</td>
              </tr>";
        if ($template_id) {
            echo "<tr>
                    <td style=' padding: 8px;'>Template ID</td>
                    <td style=' padding: 8px;'>$template_id</td>
                  </tr>";
        }
        echo "<tr>
                <td style=' padding: 8px;'>Hint</td>
                <td style=' padding: 8px;'>Open the template in WordPress and make sure it contains content.</td>
              </tr>";
        break;

    default:
        echo "<tr>
                <td style=' padding: 8px;'>Unknown Error</td>
                <td style=' padding: 8px;'>No further explanation available.</td>
              </tr>";
        echo "<tr>
                <td style=' padding: 8px;'>Hint</td>
                <td style=' padding: 8px;'>Check the parameters passed in the shortcode or URL.</td>
              </tr>";
        break;
}

echo "</table>";


$admin_email = get_option('admin_email');
?>
<p style="margin-top: 1em;">
    If this issue persists, <a href="mailto:<?php echo esc_attr($admin_email); ?>">please contact the administrator.</a>.
</p>

    </div>
</body>
</html>
