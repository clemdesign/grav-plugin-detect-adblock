
function dabDeleteDomElement(element, domIdToStop, stopRemoving){
    if(element && !stopRemoving) {
        // Remove the next element and stop at ID = domIdToStop
        dabDeleteDomElement(element.nextElementSibling, domIdToStop, element.id === domIdToStop);

        // Remove current element
        if(element.id !== domIdToStop){
            element.remove();
        }
    }
}