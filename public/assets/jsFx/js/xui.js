function x_button(text, className, id, onClick, callback = null) {
    const button = document.createElement("button");
    button.textContent = text;
    button.className = className;
    button.id = id;
    button.addEventListener("click", onClick);
    callback?.(button);
    return button;
}

function x_button_xmodal(text, className, openModal, callback = null) {
    const button = document.createElement("button");
    button.textContent = text;
    button.className = className;
    button.dataset.open = openModal;
    callback?.(button);
    return button;
}

function x_div(className, id, callback = null) {
    const div = document.createElement("div");
    div.className = className;
    div.id = id;
    callback?.(div);
    return div;
}

function x_img(src, className, id, callback = null) {
    const img = document.createElement("img");
    img.src = src;
    img.className = className;
    img.id = id;
    callback?.(img);
    return img;
}


function x_ui_badge(text, color = "primary") {
    return `<span class="badge bg-${color}">${text}</span>`;
}



















function x_create_xmodal(option = {}, htmls = {}, callback = null) {
const configDefaults = {
    size: "sm",
    icon: "",
    title: "Modal",
    subtitle: "",
    idKey: "x_modal",
    isAppended: true,
    parent: "body"
};
const sizes = {
    sm: "x_modal-content_sm",
    md: "x_modal-content_md",
    lg: "x_modal-content_lg",
    xl: "x_modal-content_xl",
    full: "x_modal-content_full"
};

const config = { ...configDefaults, ...option };

console.log(config);


const header_id = `hm_${config.idKey}`
const body_id = `bm_${config.idKey}`
const footer_id = `fm_${config.idKey}`


    const html = `
    <div id="${config.idKey}" class="x_modal">
        <div class="x_modal-content ${sizes[config.size]}">
            <div class="x_modal-header id="${header_id}">
                <h2 class="x_modal-title">${config.title}</h2>
                <p class="x_modal-subtitle">${config.subtitle}</p>
                <button class="x_modal-close" data-close="${config.idKey}">&times;</button>
            </div>
            <div class="x_modal-body" id="${body_id}">
               ${htmls.body}
            </div>
            <div class="x_modal-footer" id="${footer_id}">
                <button class="btn-secondary" data-close="${config.idKey}">Fermer</button>
            </div>
        </div>
    </div>
    `

    


    return html;
}