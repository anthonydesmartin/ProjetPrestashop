{extends file = 'page.tpl'}
{block name = 'page_content'}
    <p>
        Activez ce code de parrainage afin de beneficier d'une réduction de <span class="montants"> {$reduc}% </span> lors
    de
    votre prochain achat si il est utilisé lors d'une
        inscription!
    </p>
    <form method="post">
        <h3>{$code}</h3>
        <input class="hidden_input" type="text" name="code" value="{$code}">
        <input type="submit" class='btn_wishcraft_module' value="Activer">
    </form>
    <h1>{$cartRules}</h1>
    <table>
        <tr>
            <th>Code</th>
            <th>Status</th>
            <th>fin de validité</th>
        </tr>
        <tr>
            <td>gfdsqgrzeq</td>
            <td>Actif</td>
            <td>date</td>
        </tr>
        <tr>
            <td>gfdsqgrzefdsqsdqazq</td>
            <td>A été utilisé </td>
            <td>date</td>
        </tr>
    </table>
{/block}