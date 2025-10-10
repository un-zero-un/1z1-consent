const {default: cssVariables} = require('./variables.scss.txt');

const templateNode = document.createElement('template');
templateNode.innerHTML = `
<style id="inline-style-tag">
    *, ::before, ::after {
        box-sizing: border-box;
    }

    ${cssVariables}

    :host {
        font-family: var(--font-family), sans-serif;
    }
    
    .crumb1 {
        animation: crumb1anim 2.5s reverse forwards;
    }
    .crumb2 {
        animation: crumb2anim 2.5s reverse forwards;
    }
    .crumb3 {
        animation: crumb3anim 2.5s reverse forwards;
    }
    
    .Dialog__media:hover .crumb1,
    .PreferenceButton:hover .crumb1 {
        animation: crumb1anim 2.5s infinite;
    }
    .Dialog__media:hover .crumb2,
    .PreferenceButton:hover .crumb2 {
        animation: crumb2anim 2.5s infinite;
    }
    .Dialog__media:hover .crumb3,
    .PreferenceButton:hover .crumb3 {
        animation: crumb3anim 2.5s infinite;
    }
    
    @keyframes crumb1anim {
        from {
            translate: 0 0;
            rotate: 0;
        } to {
            translate: 5px -10px;
            rotate: -20deg;
        }
    }
    @keyframes crumb2anim {
        from {
            translate: 0 0;
            rotate: 0;
        } to {
            translate: 2px -1px;
            rotate: -20deg;
        }
    }
    @keyframes crumb3anim {
        from {
            translate: 0 0;
            rotate: 0;
        } to {
            translate: 4px 3px;
            rotate: -20deg;
        }
    }
    
    .PreferenceButton:hover ellipse {
        fill: var(--buttonBackgroundHover, var(--buttonBackground, black));
    }

    .Dialog {
        color: var(--popin-color, #505050);
        background: var(--popin-background, canvas);
        
        line-height: 1.2;
        font-family: var(--popin-font-family, var(--font-family)), sans-serif;

        position: fixed;
        inset-block-end: 0;
        inset-inline: var(--dialog-inset-inline, 0 auto);

        padding: 0;

        border: none;
        overflow: hidden;
        border-start-start-radius: .875em;
        border-start-end-radius: .875em;
        box-shadow: 0 -.2em 1em rgba(0, 0, 0, .3);
        
        z-index: calc(var(--base-z-index) + 1);
        
        @media (min-width: 45em) {
            width: var(--dialog-inline-size);
            margin: 1em;
            
            border-radius: .875em;
            box-shadow: 2px 4px 6px #00000029;
        }
        
        /*@media (max-width: 44.999em) {*/
        /*    box-shadow: 0 -.2em 1em rgba(0, 0, 0, .3);*/
        /*}*/
    }
        
    .Dialog__form {
        display: grid;
        grid-template: 'text text' 1fr 'list actions' auto / 1fr auto;
        /*gap: 1em;*/
        
        @media (min-width: 45em) {
            grid-template: 'text list' 1fr 'actions actions' auto / 1fr 15rem;
        }
    }
    
    .Dialog__text {
        grid-area: text;
        display: grid;
        grid-template: 'media title' auto 'text text' auto / 6rem 1fr;
        padding: 1em;
        
        @media (min-width: 45em) {
            grid-template-areas: 'media title' 'media text';
        }
    }
    
    .Dialog__list {
        grid-area: list;
        padding: 1em;
    }
    
    .Dialog__title {
        grid-area: title;
        margin-block: 0;
        font-size: 1.0625rem;
        font-weight: 800;
        color: inherit;
        line-height: 1.3;
    }

    .Dialog__contentText {
        grid-area: text;
        /*padding: 1em;*/
        font-size: .75rem;
    }
    
    .Dialog__list {
        grid-area: list;
    }
    
    .Dialog__listTitle {
        margin-block: .5em 1em;
        font-size: .75em;
        font-weight: 600;
        text-transform:uppercase;
    }

    .Dialog__trackers {
        --dotSize: 1rem;
        --dotSpace: .125rem;
        --trackLength: calc(var(--trackSize) * 1.5);
        --trackSize: calc(var(--dotSize) + 2 * var(--dotSpace));
    
        display: grid;
        align-content: start;
        gap: .5em;
        list-style: none;
        max-block-size: 6rem;
        overflow:auto;
        margin: 0;
        padding: 0;
        
        @media (min-width: 45em) {
            max-block-size: 7.5rem;
        }
    
        & input[type=checkbox] {
            font-size: inherit;
            appearance: none;
            position: relative;
            padding: 0;
            margin: 0;
            
            
            
            &:checked + label .Dialog__checkboxContainer::before {
                opacity: 1;
            }
    
            &:checked + label .Dialog__checkboxContainer::after {
                inset-inline-start: calc(var(--trackLength) - var(--dotSize) - var(--dotSpace));
            }
        }
    }

    .Dialog__tracker {
        display: flex;
        align-items: center;

        & label {
            cursor: pointer;
            font-size: .75em;
        }
    }

    .Dialog__checkboxContainer {
        position: relative;
        display: inline-block;
        vertical-align: middle;
        position: relative;
        inline-size: var(--trackLength);
        block-size: var(--trackSize);
        border-radius: calc(var(--trackSize) / 2);
        background: #ccc;
        
        
            
        &::before {
            content: '';
            display: block;
            position: absolute;
            pointer-events: none;
            cursor: pointer;
            background: #bada55;
            inline-size: var(--trackLength);
            block-size: var(--trackSize);
            border-radius: calc(var(--trackSize) / 2);
            opacity: 0;
            transition: opacity .5s;
        }

        &::after {
            content: '';
            cursor: pointer;
            display: block;
            position: inherit;
            inset-block-start: var(--dotSpace);
            inset-inline-start: var(--dotSpace);
            /*background: white;*/
            inline-size: var(--dotSize);
            aspect-ratio: 1;
            border: 5px solid white;
            border-radius: 50%;
            filter: drop-shadow(0px 3px 4px #00000029);
            transition: .5s;
        }
    }

    .Dialog__trackerName {
        display: inline-block;
        vertical-align: middle;
        padding-inline-start: .5em;
    }

    .Dialog__actions {
        grid-area: actions;
        display: grid;
        
        @media (min-width: 45em) {
            display: flex;
        }
    }
    
    .Dialog__media {
        grid-area: media;
        inline-size: 5rem;
        block-size: 5rem;
        align-self: center;
    }

    .Dialog__action {
        flex: 1;
        padding: 1em;
        background: white;
        cursor: pointer;
        color: inherit;
        border: 1px solid #ccc;
        border-block-end: none;
        
        @media (max-width: 44.9em) {
            &:not(:first-child) {
                border-block-start: 1px solid #ccc;
            }
        }
        
        @media (min-width: 45em) {
            border: none;
            border-block-start: 1px solid #ccc;
        }

        &.Dialog__action--strong {
            font-weight: bold;
        }

        &.Dialog__action--middle {
            border: 1px solid #ccc;
            border-block-end: none;
        }

        &:hover {
            background: #eee;
        }
    }

    .PreferenceButton {
        position: fixed;
        inset-block-end: 1rem;
        inset-inline-start: 1rem;
        
        display: grid;
        grid-template-columns: 3.625rem 0rem;
        grid-template-rows: 3.625rem;
        block-size: 3.625rem;
        align-items: center;
        padding: 0rem;
        margin: 0rem;
        color: var(--buttonColor, white);
        font: .75rem/1.2 sans-serif;
        text-align: start;
        background: var(--buttonBackground, black);
        border: none;
        border-radius: 1.8rem;
        overflow:hidden;
        transition: all .3s;
        
        cursor: pointer;
        
        z-index: var(--base-z-index);
        
        &:focus-visible,
        &:hover {
            grid-template-columns: 3.625rem 10rem;
            background: var(--buttonBackgroundHover, black);
        }
        
        & svg {
            display: block;
            inline-size: 3.625rem;
            block-size: 3.625rem;
        }
        
        & span {
            inline-size: 10rem;
            padding-inline: 1em;
        }
    }
</style>
<dialog id="consent-dialog" class="Dialog">
    <form action="#" id="form" class="Dialog__form">
        <div class="Dialog__text">
        
            <svg xmlns="http://www.w3.org/2000/svg" width="57" height="58" viewBox="0 0 57 58" class="Dialog__media">
              <g transform="translate(-0.494 0.333)">
                <ellipse cx="28.5" cy="29" rx="28.5" ry="29" transform="translate(0.494 -0.333)" fill="var(--buttonBackground, black)"/>
                <path d="M20.8,41.6A20.8,20.8,0,0,1,6.092,6.092,20.816,20.816,0,0,1,30.28,2.281a4.463,4.463,0,0,0,.03,4.4,4.458,4.458,0,0,0,1.01,5.653,4.46,4.46,0,0,0,4.338,5.489c.076,0,.158,0,.251-.007A4.461,4.461,0,0,0,41.6,20.547c0,.062,0,.124,0,.185V20.8A20.8,20.8,0,0,1,20.8,41.6Zm0-31.2A10.4,10.4,0,1,0,31.2,20.8,10.412,10.412,0,0,0,20.8,10.4Z" transform="translate(12.752 4.349) rotate(11)" fill="var(--buttonShapeColor, #ffdb77)"/>
              </g>
              <g class="crumb1">
                <path d="M2.419,0,4.837,4.837H0Z" transform="matrix(0.934, 0.358, -0.358, 0.934, 47.732, 17.499)" fill="var(--buttonShapeColor, #ffdb77)"/>
              </g>
              <g class="crumb2">
                <path d="M1.966,0,3.932,3.466H0Z" transform="matrix(0.934, 0.358, -0.358, 0.934, 53.117, 23.485)" fill="var(--buttonShapeColor, #ffdb77)"/>
              </g>
              <g class="crumb3">
                <path d="M1.534,0,3.069,4.846H0Z" transform="matrix(-0.53, 0.848, -0.848, -0.53, 53.228, 29.34)" fill="var(--buttonShapeColor, #ffdb77)"/>
              </g>
            </svg>
            
            <h2 class="Dialog__title" slot="DialogTitle">Hello, on a besoin de votre permission</h2>
            <div class="Dialog__contentText" slot="DialogText">
                <p>
                    On aimerait utiliser des cookies pour améliorer votre expérience sur notre site. 
                </p>
                <p>
                    Vous nous donnez votre autorisation ? Quelle que soit votre réponse, on ne vous embêtera plus avec cette question 🙂.
                </p>
            </div>
        </div>
        
        <div class="Dialog__list">
            <h3 class="Dialog__listTitle">Les cookies en question :</h3>
            <ul id="trackers" class="Dialog__trackers"></ul>
        </div>
        
        <div class="Dialog__actions">
            <button class="Dialog__action" type="submit" id="decline-all">Refuser Tout</button>
            <button class="Dialog__action Dialog__action--middle" type="submit" id="accept-selection">Accepter ma sélection</button>
            <button class="Dialog__action Dialog__action--strong" type="submit" id="accept-all">Accepter Tout</button>
        </div>
    </form>
</dialog>

<button class="PreferenceButton" id="show-consent-dialog">
    <svg xmlns="http://www.w3.org/2000/svg" width="57" height="58" viewBox="0 0 57 58">
      <g transform="translate(-0.494 0.333)" class="svgContainer">
        <ellipse cx="28.5" cy="29" rx="28.5" ry="29" transform="translate(0.494 -0.333)" fill="var(--buttonBackground, black)"/>
        <path d="M20.8,41.6A20.8,20.8,0,0,1,6.092,6.092,20.816,20.816,0,0,1,30.28,2.281a4.463,4.463,0,0,0,.03,4.4,4.458,4.458,0,0,0,1.01,5.653,4.46,4.46,0,0,0,4.338,5.489c.076,0,.158,0,.251-.007A4.461,4.461,0,0,0,41.6,20.547c0,.062,0,.124,0,.185V20.8A20.8,20.8,0,0,1,20.8,41.6Zm0-31.2A10.4,10.4,0,1,0,31.2,20.8,10.412,10.412,0,0,0,20.8,10.4Z" transform="translate(12.752 4.349) rotate(11)" fill="var(--buttonShapeColor, #ffdb77)"/>
      </g>
      <g class="crumb1">
        <path d="M2.419,0,4.837,4.837H0Z" transform="matrix(0.934, 0.358, -0.358, 0.934, 47.732, 17.499)" fill="var(--buttonShapeColor, #ffdb77)"/>
      </g>
      <g class="crumb2">
        <path d="M1.966,0,3.932,3.466H0Z" transform="matrix(0.934, 0.358, -0.358, 0.934, 53.117, 23.485)" fill="var(--buttonShapeColor, #ffdb77)"/>
      </g>
      <g class="crumb3">
        <path d="M1.534,0,3.069,4.846H0Z" transform="matrix(-0.53, 0.848, -0.848, -0.53, 53.228, 29.34)" fill="var(--buttonShapeColor, #ffdb77)"/>
      </g>
    </svg>
    <span>
        Changer mes préférences cookie
    </span>
</button>
`;

export default templateNode;
