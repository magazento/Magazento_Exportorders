varienTabs.prototype.initialize = function(containerId, destElementId,  activeTabId, shadowTabs){
    this.containerId    = containerId;
    this.destElementId  = destElementId;
    this.activeTab = null;

    this.tabOnClick     = this.tabMouseClick.bindAsEventListener(this);

    this.tabs = [];
    $(this.containerId).childElements().each(function(el){
        el.childElements().each(function(elInner){
            if (elInner.tagName != 'A') {
                return;
            }
            this.tabs.push(elInner);
        }, this)
    }, this);

    this.hideAllTabsContent();
    for (var tab=0; tab<this.tabs.length; tab++) {
        Event.observe(this.tabs[tab],'click',this.tabOnClick);
        // move tab contents to destination element
        if($(this.destElementId)){
            var tabContentElement = $(this.getTabContentElementId(this.tabs[tab]));
            if(tabContentElement && tabContentElement.parentNode.id != this.destElementId){
                $(this.destElementId).appendChild(tabContentElement);
                tabContentElement.container = this;
                tabContentElement.statusBar = this.tabs[tab];
                tabContentElement.tabObject  = this.tabs[tab];
                this.tabs[tab].contentMoved = true;
                this.tabs[tab].container = this;
                this.tabs[tab].show = function(){
                    this.container.showTabContent(this);
                }
                if(varienGlobalEvents){
                    varienGlobalEvents.fireEvent('moveTab', {tab:this.tabs[tab]});
                }
            }
        }

        // bind shadow tabs
        if (this.tabs[tab].id && shadowTabs && shadowTabs[this.tabs[tab].id]) {
            this.tabs[tab].shadowTabs = shadowTabs[this.tabs[tab].id];
        }
    }

    this.displayFirst = activeTabId;
    Event.observe(window,'load',this.moveTabContentInDest.bind(this));
}
