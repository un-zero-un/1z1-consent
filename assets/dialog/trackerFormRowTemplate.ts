const templateNode = document.createElement('template');
templateNode.innerHTML = `
<li class="Dialog__tracker">
    <input type="checkbox" data-tracker-checkbox value="1" />
    <label data-tracker-label>
        <span class="Dialog__checkboxContainer"></span>
        <span data-tracker-name class="Dialog__trackerName"></span>
    </label>
</li>
`;

export default templateNode;
