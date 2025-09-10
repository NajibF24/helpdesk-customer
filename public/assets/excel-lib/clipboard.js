// Clipboard Manager
class SpreadsheetClipboard {
    constructor() {
      this.clipboardData = null
      this.listeners = new Set()
    }
  
    copy(cellRef, value, displayValue) {
      this.clipboardData = {
        cellRef,
        value,
        displayValue,
        timestamp: Date.now(),
      }
      this.notifyListeners()
    }
  
    paste() {
      return this.clipboardData
    }
  
    clear() {
      this.clipboardData = null
      this.notifyListeners()
    }
  
    hasCopiedData() {
      return this.clipboardData !== null
    }
  
    getCopiedCellRef() {
      return this.clipboardData?.cellRef || null
    }
  
    addListener(callback) {
      this.listeners.add(callback)
    }
  
    removeListener(callback) {
      this.listeners.delete(callback)
    }
  
    notifyListeners() {
      this.listeners.forEach((callback) => callback())
    }
  }
  
  // Global instance
  window.clipboard = new SpreadsheetClipboard()
  