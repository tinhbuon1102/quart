<?php


if( isset($account_number) && $account_number || isset($bank_name) && $bank_name|| isset($sort_code) && $sort_code || isset($iban) && $iban || isset($bic) && $bic) {
   
    ?>
    <tr class="yith-wcpmr-bacs-account">
        <td class="sort"></td>
        <td><input type="text" class="<?php echo isset($bank_woo) ? $bank_woo : 'yith_wcpmr_bacs_account_name' ?>"
                   value="<?php echo isset ($account_name) ? esc_attr(wp_unslash($account_name)) : '' ?>"
                   name=<?php echo isset($bank_woo) ? 'woo_account_name[]' : 'account_name[]' ?>/></td>
        <td><input type="text" value="<?php echo isset ($account_number) ? esc_attr($account_number) : '' ?>"
                   name=<?php echo isset($bank_woo) ? 'woo_account_number[]' : 'account_number[]' ?>/></td>
        <td><input type="text" value="<?php echo isset ($bank_name) ? esc_attr(wp_unslash($bank_name)) : '' ?>"
                   name=<?php echo isset($bank_woo) ? 'woo_bank_name[]' : 'bank_name[]' ?>/></td>
        <td><input type="text" value="<?php echo isset ($sort_code) ? esc_attr($sort_code) : '' ?>"
                   name=<?php echo isset($bank_woo) ? 'woo_sort_code[]' : 'sort_code[]' ?>/></td>
        <td><input type="text" value="<?php echo isset ($iban) ? esc_attr($iban) : '' ?>"
                   name=<?php echo isset($bank_woo) ? 'woo_iban[]' : 'iban[]' ?>/></td>
        <td><input type="text" value="<?php echo isset ($bic) ? esc_attr($bic) : '' ?>"
                   name=<?php echo isset($bank_woo) ? 'woo_bic[]' : 'bic[]' ?>/></td>
    </tr>

    <?php
}
?>