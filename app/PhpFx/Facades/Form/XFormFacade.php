<?php
namespace App\PhpFx\Facades\Form;

use App\PhpFx\Form\XForm;

class XFormFacade
{
    private static $xformInstance = null;

    private static function xform()
    {
        if (self::$xformInstance === null) {
            self::$xformInstance = new XForm(true);
        }
        return self::$xformInstance;
    }

    // Champ de base avec rendu
    private static function renderBasicField($type, $name, $label = '', $value = '', $attributes = [])
    {
        return self::xform()
            ->field($name, [
                'type' => $type,
                'label' => $label,
                'value' => $value,
                'attribute' => $attributes
            ])
            ->renderField($name);
    }

    // =============================================
    // CHAMPS DE FORMULAIRE DE BASE
    // =============================================

    public static function text($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('text', $name, $label, $value, $attributes);
    }

    public static function email($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('email', $name, $label, $value, $attributes);
    }

    public static function password($name, $label = '', $attributes = []) {
        return self::renderBasicField('password', $name, $label, '', $attributes);
    }

    public static function hidden($name, $value = '', $attributes = []) {
        return self::renderBasicField('hidden', $name, '', $value, $attributes);
    }

    public static function number($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('number', $name, $label, $value, $attributes);
    }

    public static function tel($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('tel', $name, $label, $value, $attributes);
    }

    public static function url($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('url', $name, $label, $value, $attributes);
    }

    public static function search($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('search', $name, $label, $value, $attributes);
    }

    public static function color($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('color', $name, $label, $value, $attributes);
    }

    public static function range($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('range', $name, $label, $value, $attributes);
    }

    public static function date($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('date', $name, $label, $value, $attributes);
    }

    public static function time($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('time', $name, $label, $value, $attributes);
    }

    public static function datetime($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('datetime-local', $name, $label, $value, $attributes);
    }

    public static function month($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('month', $name, $label, $value, $attributes);
    }

    public static function week($name, $label = '', $value = '', $attributes = []) {
        return self::renderBasicField('week', $name, $label, $value, $attributes);
    }

    // =============================================
    // CHAMPS COMPLEXES
    // =============================================

    public static function textarea($name, $label = '', $value = '', $attributes = []) {
        return self::xform()
            ->field($name, [
                'type' => 'textarea',
                'label' => $label,
                'value' => $value,
                'attribute' => $attributes
            ])
            ->renderField($name);
    }

    public static function select($name, $label = '', $options = [], $selected = '', $attributes = []) {
        return self::xform()
            ->field($name, [
                'type' => 'select',
                'label' => $label,
                'options' => $options,
                'valueSelected' => $selected,
                'attribute' => $attributes
            ])
            ->renderField($name);
    }

    public static function checkbox($name, $label = '', $checked = false, $value = '1', $attributes = [], $class = '', $textSelected = '')
     {
        $attributes = array_merge($attributes, ['checked' => $checked]);
        return self::xform()
            // ->field($name, [
            //     'type' => 'checkbox',
            //     'label' => $label,
            //     'value' => $value,
            //     'attribute' => $checked ? array_merge($attributes, ['checked' => true]) : $attributes
            // ])
            ->checkbox($name, $label, $value, $attributes, $class, $textSelected)
            ->renderField($name);
    }

    public static function radio($name, $label = '', $options = [], $selected = '', $attributes = []) {
        return self::xform()
            ->field($name, [
                'type' => 'radio',
                'label' => $label,
                'options' => $options,
                'valueSelected' => $selected,
                'attribute' => $attributes
            ])
            ->renderField($name);
    }

    public static function file($name, $label = '', $attributes = []) {
        return self::xform()
            ->field($name, [
                'type' => 'file',
                'label' => $label,
                'attribute' => $attributes
            ])
            ->renderField($name);
    }

    // =============================================
    // BOUTONS
    // =============================================

    public static function submit($value = 'Submit', $attributes = []) {
        return self::xform()
            ->field('submit', [
                'type' => 'submit',
                'value' => $value,
                'attribute' => $attributes
            ])
            ->renderField('submit');
    }

    public static function button($value = 'Button', $attributes = []) {
        return self::xform()
            ->field('button', [
                'type' => 'button',
                'value' => $value,
                'attribute' => $attributes
            ])
            ->renderField('button');
    }

    // =============================================
    // CHAMPS AVANCÉS (avec JS intégré)
    // =============================================

    public static function filePreview($name, $label, $types = 'image/*', $previewSize = '150px', $attributes = []) {
        $id = $attributes['id'] ?? $name;
        $field = self::file($name, $label, array_merge($attributes, ['id' => $id]));
        
        $html = '<div class="file-preview-wrapper">';
        $html .= str_replace(
            '<input',
            '<input accept="'.$types.'"',
            $field
        );
        $html .= '<div class="preview-container mt-2" style="width:'.$previewSize.';height:'.$previewSize.'">';
        $html .= '<img id="'.$id.'-preview" src="#" alt="Preview" class="img-thumbnail d-none" style="max-width:100%;max-height:100%">';
        $html .= '</div>';
        $html .= self::jsFilePreview($id);
        $html .= '</div>';
        
        return $html;
    }

    private static function jsFilePreview($id)
    {
        return <<<JS
<script>
document.getElementById("{$id}").addEventListener("change", function(e) {
    const reader = new FileReader();
    const preview = document.getElementById("{$id}-preview");
    
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.classList.remove("d-none");
    }
    
    if(this.files[0]) reader.readAsDataURL(this.files[0]);
});
</script>
JS;
    }

    public static function multiSelect($name, $label, $options, $selected = [], $attributes = []) {
        $id = $attributes['id'] ?? $name;
        $field = self::select($name, $label, $options, $selected, array_merge($attributes, ['multiple' => true]));
        
        $html = '<div class="multi-select-wrapper">';
        $html .= str_replace(
            '<select',
            '<select id="'.$id.'-select"',
            $field
        );
        $html .= '<div id="'.$id.'-badges" class="d-flex flex-wrap gap-2 mt-2"></div>';
        $html .= '<input type="hidden" name="'.$name.'" id="'.$id.'" value="'.implode(',', $selected).'">';
        $html .= self::jsMultiSelect($id);
        $html .= '</div>';
        
        return $html;
    }

    private static function jsMultiSelect($id)
    {
        return <<<JS
<script>
document.getElementById("{$id}-select").addEventListener("change", function() {
    const badgesContainer = document.getElementById("{$id}-badges");
    const hiddenInput = document.getElementById("{$id}");
    const selectedValues = Array.from(this.selectedOptions).map(opt => opt.value);
    
    badgesContainer.innerHTML = "";
    selectedValues.forEach(value => {
        const text = this.querySelector(`option[value="\${value}"]`).text;
        badgesContainer.innerHTML += `
            <span class="badge bg-primary d-flex align-items-center">
                \${text}
                <button type="button" class="btn-close btn-close-white ms-2" data-value="\${value}"></button>
            </span>`;
    });
    
    hiddenInput.value = selectedValues.join(",");
    
    badgesContainer.querySelectorAll("button").forEach(btn => {
        btn.addEventListener("click", function() {
            const value = this.getAttribute("data-value");
            const option = document.querySelector(`#{$id}-select option[value="\${value}"]`);
            option.selected = false;
            btn.parentElement.remove();
            hiddenInput.value = Array.from(document.getElementById("{$id}-select").selectedOptions)
                .map(opt => opt.value).join(",");
        });
    });
});
</script>
JS;
    }

    // =============================================
    // STRUCTURES COMPLEXES
    // =============================================

   


    public static function ajaxForm($formId, $options = []) {
        $defaults = [
            'success' => 'function(response) { console.log("Success:", response); }',
            'error' => 'function(error) { console.error("Error:", error); }',
            'beforeSend' => 'function() { /* Show loader */ }'
        ];
        
        $options = array_merge($defaults, $options);
        
        return <<<JS
<script>
document.getElementById("{$formId}").addEventListener("submit", function(e) {
    e.preventDefault();
    
    {$options['beforeSend']}
    
    fetch(this.action, {
        method: this.method,
        body: new FormData(this),
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        {$options['success']}
    })
    .catch(error => {
        {$options['error']}
    });
});
</script>
JS;
    }

    // =============================================
    // MÉTHODES UTILITAIRES
    // =============================================

    public static function open($action = '', $method = 'POST', $attributes = []) {
        return self::xform()
            ->form(array_merge($attributes, [
                'action' => $action,
                'method' => $method
            ]))
            ->open();
    }

    public static function close() {
        return self::xform()->close();
    }

    public static function token() {
        return self::xform()->csrf()->token();
    }

    public static function setBootstrap($enable = true) {
        self::xform()->bootstrap($enable);
        return new static;
    }

    public static function mapOptions($data, $value, $text, $selectedText = null) {
        return self::xform()->mapOptions($data, $value, $text, $selectedText);
    }

    public static function class(){
        return self::xform();
    }

    public static function self(){
        return new self;
    }
}