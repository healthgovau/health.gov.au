<div class="wrapper">
    <div class="field field-name-title field-type-ds field-label-hidden">
        <div class="field-items">
            <div class="field-item even" property="dc:title">
                <h3>
                    <a href="<?php print $display_url ?>" title="<?php print $live_url ?>"><?php print $title ?></a>
                </h3>
            </div>
        </div>
    </div>
    <div class="field field-name-field-date-updated field-type-datetime field-label-hidden">
        <div class="field-items">
            <div class="field-item even">
                <span class="date-display-single" property="dc:date" datatype="xsd:dateTime"><?php print format_date($date/1000, 'govcms_month_day_year'); ?></span>
            </div>
        </div>
    </div>
    <div class="field field-name-field-summary field-type-text-long field-label-hidden">
        <div class="field-items">
            <div class="field-item even">
                <p><?php print $summary; ?>></p>
            </div>
        </div>
    </div>
</div>
