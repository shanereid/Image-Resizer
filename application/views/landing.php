<?=$head?>
        <div id="outerWrapper">
            <div class="formatBox">
                <h3>Available Formats:</h3>
                <table cellspacing="0">
                    <tr class="header">
                        <th>
                        <th width="200">Name</th>
                        <th width="100" class="middle">Width</th>
                        <th width="100">Height</th>
                    </tr>
                    <?php foreach($presets as $preset): ?>
                    <tr>
                        <td><input type="checkbox" name="presets[]" value="<?=$preset['id']?>" checked="checked"/></td>
                        <td><?=$preset['name']?></td>
                        <td class="middle"><?=$preset['image_w']?>px</td>
                        <td><?=$preset['image_h']?>px</td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <a class="save" id="fileUpload"><span class="loader"></span><span class="text">Resize Image[s]</span></a>
                <div class="clear"></div>
            </div>
        </div>
<?=$foot?>