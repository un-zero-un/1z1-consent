import * as monaco from 'monaco-editor';

self.MonacoEnvironment = {
    getWorkerUrl: function (moduleId, label) {
        if (label === 'json') {
            return '/build/json.worker.js';
        }
        if (label === 'css' || label === 'scss' || label === 'less') {
            return '/build/css.worker.js';
        }
        if (label === 'html' || label === 'handlebars' || label === 'razor') {
            return '/build/html.worker.js';
        }
        if (label === 'typescript' || label === 'javascript') {
            return '/build/ts.worker.js';
        }

        return '/build/editor.worker.js';
    }
};

function initEditor() {
    document
        .querySelectorAll('textarea[data-monaco-editor]')
        .forEach(function (element) {
            if ('true' === element.dataset.monacoInitialized) {
                return;
            }

            const editor = document.createElement('div');
            editor.style.height = '20em';
            editor.style.width = '100%';

            element.parentElement.appendChild(editor);
            element.dataset.monacoInitialized = 'true';

            const monacoInstance = monaco.editor.create(editor, {
                value: element.textContent,
                language: element.dataset.monacoLanguage || 'javascript',
                theme: 'vs-dark',
                minimap: { enabled: false },
            });

            monacoInstance.onDidChangeModelContent(function () {
                element.textContent = monacoInstance.getValue();
            });

            element.style.display = 'none';
        });
}

document.addEventListener('DOMContentLoaded', () => {
    initEditor();
});

document.addEventListener('ea.collection.item-added', () => {
    initEditor();
})
