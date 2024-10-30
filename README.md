#Action
1. data-trigger
    Define the trigger of the action. There are two triggers: *click* and *change*
2. data-trigger-value
    In case of *change* event, there must be a value that when the input value matches this value, 
    the action will be triggered
3. data-action
    Define the action of the element. The actions are:
      * Open URL
      * Show an element
      * Go to next step (in multiple step form)
      * Go to previous step (in multiple step form)
      * Submit form
4. data-target
    The target (element-id/URL) of the action. This meta data is only needed when
      * Open URL
      * Show an element
      * Hide an element
      
5. Example
    * Open URL
        > data-trigger="click" data-action="open-url" data-target="http://core37.com"
        
    * Show an element on click
        > data-trigger="click" data-action="show-element" data-target="#element-id"
        
    * Show an element on change
        > data-trigger="change" data-trigger-value="trigger-value" data-action="show-element" data-target="#element-id"
        
    * Hide an element on change
        > data-trigger="change" data-trigger-value="trigger-value" data-action="hide-element data-target="#element-id"
            
    * Go to next step on click
        > data-trigger="click" data-action="next-step"
    * Submit form
        > data-trigger="click" data-action="submit"
        