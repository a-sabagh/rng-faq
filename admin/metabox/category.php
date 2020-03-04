<?php
if (isset($response) && is_wp_error($response)) {
    $message = $response->get_error_message();
    $styles = "style='direction:ltr;text-align:left;border-left:1px solid red;padding-left:5px;'";
    echo "<p {$styles}>{$message}</p>";
} elseif (isset($response)) {
    $product = json_decode($response['body'], true);
}
?>
<table class="form-table">
    <tbody>
        <tr class="form-field form-required term-name-wrap">
            <th scope="row"><label for="name">محصول مرتبط استورینا</label></th>
            <td>
                <select name="faq_product_id" style="width: 100%;" class="faq-select2">
                    <?php
                    if (isset($response) && !is_wp_error($response) && is_numeric($product['id'])) {
                        echo '<option value="' . $product['id'] . '" >' . $product['text'] . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
    </tbody>
</table>