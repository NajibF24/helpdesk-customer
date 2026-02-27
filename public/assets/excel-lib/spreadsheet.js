$(document).ready(() => {
  let ROWS = 10
  let COLS = 6

  const cells = {}
  let selectedCell = "A1"
  let isDragging = false
  let dragStart = ""
  let dragEnd = ""
  let copiedCell = ""

  // Declare variables
  const $ = window.$ // Assuming jQuery is available globally
  const clipboard = window.clipboard // Assuming clipboard is available globally
  const formulaParser = window.formulaParser // Assuming formulaParser is available globally

  function initSpreadsheet(rows = 10, cols = 6) {
		ROWS = rows
		COLS = cols
    generateGrid()
    bindEvents()
    selectCell("A1")

    // Listen for clipboard changes
    clipboard.addListener(updateCopiedCell)
    // Expose globally so it can be invoked from other scripts
    window.initSpreadsheet = initSpreadsheet
  }

  // Generate the spreadsheet grid
  function generateGrid() {
    const grid = $("#spreadsheetGrid")
    grid.empty()

    // Header row
    const headerRow = $('<div class="grid-row header"></div>')
    headerRow.append('<div class="cell header row-header"></div>')

    for (let i = 0; i < COLS; i++) {
      const colName = formulaParser.getColumnName(i)
      headerRow.append(`<div class="cell header">${colName}</div>`)
    }
    grid.append(headerRow)

    // Data rows
    for (let row = 1; row <= ROWS; row++) {
      const dataRow = $('<div class="grid-row"></div>')
      dataRow.append(`<div class="cell header row-header">${row}</div>`)

      for (let col = 0; col < COLS; col++) {
        const colName = formulaParser.getColumnName(col)
        const cellRef = `${colName}${row}`
        const cell = $(`<div class="cell" data-cell-ref="${cellRef}"></div>`)
        dataRow.append(cell)
      }
      grid.append(dataRow)
    }
  }

  // Bind all event handlers
  function bindEvents() {
    // Cell click
    $(document).on("click", ".cell[data-cell-ref]", function (e) {
      const cellRef = $(this).data("cell-ref")
      if (!$(e.target).hasClass("drag-handle")) {
        selectCell(cellRef)
        startEditing(cellRef)
      }
    })

    // Cell double click
    $(document).on("dblclick", ".cell[data-cell-ref]", function () {
      const cellRef = $(this).data("cell-ref")
      selectCell(cellRef)
      startEditing(cellRef)
    })

    // Formula input
    $("#formulaInput").on("input", function () {
      const value = $(this).val()
      updateCell(selectedCell, value)
    })

    $("#formulaInput").on("keydown", (e) => {
      if (e.key === "Enter") {
        stopEditing()
        moveSelection(0, 1) // Move down
      } else if (e.key === "Escape") {
        stopEditing()
      }
    })

    // Keyboard shortcuts
    $(document).on("keydown", (e) => {
      if (e.ctrlKey || e.metaKey) {
        switch (e.key.toLowerCase()) {
          case "c":
            e.preventDefault()
            copyCell()
            break
          case "v":
            e.preventDefault()
            pasteCell()
            break
          case "x":
            e.preventDefault()
            cutCell()
            break
        }
      }

      // Arrow keys
      if (!$("#formulaInput").is(":focus")) {
        switch (e.key) {
          case "ArrowUp":
            e.preventDefault()
            moveSelection(0, -1)
            break
          case "ArrowDown":
            e.preventDefault()
            moveSelection(0, 1)
            break
          case "ArrowLeft":
            e.preventDefault()
            moveSelection(-1, 0)
            break
          case "ArrowRight":
            e.preventDefault()
            moveSelection(1, 0)
            break
          case "Delete":
          case "Backspace":
              // Only hijack Delete/Backspace when the event originates from the spreadsheet grid
              if (!$("#formulaInput").is(":focus") && $(e.target).closest("#spreadsheetGrid").length) {
              e.preventDefault()
              updateCell(selectedCell, "")
            }
            break
        }
      }
    })

    // Context menu
    $(document).on("contextmenu", ".cell[data-cell-ref]", function (e) {
      e.preventDefault()
      const cellRef = $(this).data("cell-ref")
      selectCell(cellRef)
      showContextMenu(e.pageX, e.pageY)
    })

    // Context menu actions
    $(document).on("click", ".context-menu-item", function () {
      const action = $(this).data("action")
      switch (action) {
        case "copy":
          copyCell()
          break
        case "paste":
          pasteCell()
          break
        case "cut":
          cutCell()
          break
      }
      hideContextMenu()
    })

    // Hide context menu on click outside
    $(document).on("click", () => {
      hideContextMenu()
    })

    // Drag handle events
    $(document).on("mousedown", ".drag-handle", (e) => {
      e.preventDefault()
      e.stopPropagation()
      startDrag(e)
    })

    // Export button events
    $("#btnPrintData").on("click", () => {
      printSpreadsheetData()
      alert("Data printed to browser console! Open Developer Tools (F12) to see it.")
    })

    $("#btnDownloadCSV").on("click", () => {
      downloadData("csv")
    })

    $("#btnDownloadJSON").on("click", () => {
      downloadData("json")
    })

    $("#btnGetData").on("click", () => {
      const data = getAllData()
      console.log("Current spreadsheet data:", data)
      alert(`Found ${data.length} rows of data. Check console for details.`)
    })
  }

  // Select a cell
  function selectCell(cellRef) {
    // Remove previous selection
    $(".cell").removeClass("selected")
    $(".drag-handle").remove()

    selectedCell = cellRef

    // Add selection to new cell
    const cell = $(`.cell[data-cell-ref="${cellRef}"]`)
    cell.addClass("selected")

    // Add drag handle
    cell.append('<div class="drag-handle" title="Drag to fill cells"></div>')

    // Update formula bar
    $("#cellName").text(cellRef)
    const cellData = cells[cellRef]
    $("#formulaInput").val(cellData ? cellData.value : "")
  }

  // Start editing a cell
  function startEditing(cellRef) {
    $("#formulaInput").focus()
  }

  // Stop editing
  function stopEditing() {
    $("#formulaInput").blur()
  }

  // Move selection
  function moveSelection(colDelta, rowDelta) {
    const parsed = formulaParser.parseCellReference(selectedCell)
    const newCol = Math.max(0, Math.min(COLS - 1, parsed.colIndex + colDelta))
    const newRow = Math.max(1, Math.min(ROWS, parsed.row + rowDelta))
    const newCellRef = `${formulaParser.getColumnName(newCol)}${newRow}`
    selectCell(newCellRef)
  }

  // Update cell value
  function updateCell(cellRef, value) {
    if (value === "") {
      delete cells[cellRef]
    } else {
      cells[cellRef] = {
        value: value,
        displayValue: value.startsWith("=") ? "Calculating..." : value,
      }
    }

    // Recalculate all formulas
    recalculateFormulas()

    // Update display
    updateCellDisplay(cellRef)
  }

  // Recalculate all formulas
  function recalculateFormulas() {
    Object.keys(cells).forEach((cellRef) => {
      if (cells[cellRef].value.startsWith("=")) {
        try {
          const result = formulaParser.parseFormula(cells[cellRef].value, getCellValue)
          cells[cellRef].displayValue = String(result)
        } catch (e) {
          cells[cellRef].displayValue = "#ERROR"
        }
      }
    })

    // Update all cell displays
    Object.keys(cells).forEach((cellRef) => {
      updateCellDisplay(cellRef)
    })
  }

  // Get cell value
  function getCellValue(cellRef) {
    return cells[cellRef]?.value || ""
  }

  // Get cell display value
  function getCellDisplayValue(cellRef) {
    const cellData = cells[cellRef]
    if (!cellData) return ""

    if (cellData.value.startsWith("=")) {
      return cellData.displayValue
    }

    return cellData.value
  }

  // Update cell display
  function updateCellDisplay(cellRef) {
    const cell = $(`.cell[data-cell-ref="${cellRef}"]`)
    const displayValue = getCellDisplayValue(cellRef)
    cell.text(displayValue)
  }

  // Copy cell
  function copyCell() {
    const cellData = cells[selectedCell]
    if (cellData) {
      clipboard.copy(selectedCell, cellData.value, cellData.displayValue)
    } else {
      clipboard.copy(selectedCell, "", "")
    }
  }

  // Paste cell
  function pasteCell() {
    const clipboardData = clipboard.paste()
    if (!clipboardData) return

    let pasteValue = clipboardData.value

    // If it's a formula, adjust the cell references
    if (pasteValue.startsWith("=")) {
      pasteValue = formulaParser.adjustFormulaReferences(pasteValue, clipboardData.cellRef, selectedCell)
    }

    updateCell(selectedCell, pasteValue)
  }

  // Cut cell
  function cutCell() {
    copyCell()
    updateCell(selectedCell, "")
  }

  // Update copied cell display
  function updateCopiedCell() {
    // Remove previous copy indicators
    $(".cell").removeClass("copied")
    $(".copy-indicator").remove()

    const newCopiedCell = clipboard.getCopiedCellRef()
    if (newCopiedCell) {
      copiedCell = newCopiedCell
      const cell = $(`.cell[data-cell-ref="${copiedCell}"]`)
      cell.addClass("copied")
      cell.append('<div class="copy-indicator"></div>')

      // Show status
      showStatus(`Copied: ${copiedCell} - Press Ctrl+V to paste`, "copy")
    } else {
      copiedCell = ""
      hideStatus()
    }
  }

  // Show context menu
  function showContextMenu(x, y) {
    const menu = $("#contextMenu")
    const canPaste = clipboard.hasCopiedData()

    menu.find('[data-action="paste"]').toggleClass("disabled", !canPaste)
    menu.css({ left: x, top: y }).show()
  }

  // Hide context menu
  function hideContextMenu() {
    $("#contextMenu").hide()
  }

  // Show status message
  function showStatus(message, type = "") {
    const statusBar = $("#statusBar")
    statusBar.removeClass("drag copy").addClass(type)
    statusBar.html(`<strong>${message}</strong>`).show()
  }

  // Hide status message
  function hideStatus() {
    $("#statusBar").hide()
  }

  // Start drag operation
  function startDrag(e) {
    isDragging = true
    dragStart = selectedCell
    dragEnd = selectedCell

    $("body").css("cursor", "crosshair").css("user-select", "none")
    showStatus(`Auto-filling: ${dragStart} → ${dragEnd}`, "drag")

    $(document).on("mousemove.drag", (e) => {
      // Find cell under mouse
      const elementUnderMouse = document.elementFromPoint(e.clientX, e.clientY)
      const cellElement = $(elementUnderMouse).closest("[data-cell-ref]")

      if (cellElement.length) {
        const cellRef = cellElement.data("cell-ref")
        if (cellRef && cellRef !== dragEnd) {
          dragEnd = cellRef
          updateDragTargets()
          showStatus(`Auto-filling: ${dragStart} → ${dragEnd} (${getDragRange().length} cells)`, "drag")
        }
      }
    })

    $(document).on("mouseup.drag", () => {
      $("body").css("cursor", "").css("user-select", "")

      if (dragStart && dragEnd && dragStart !== dragEnd) {
        fillDragRange()
      }

      // Clean up
      $(".cell").removeClass("drag-target")
      isDragging = false
      dragStart = ""
      dragEnd = ""
      hideStatus()

      $(document).off(".drag")
    })
  }

  // Update drag targets
  function updateDragTargets() {
    $(".cell").removeClass("drag-target")

    if (isDragging && dragStart && dragEnd) {
      const range = getDragRange()
      range.forEach((cellRef) => {
        if (cellRef !== dragStart) {
          $(`.cell[data-cell-ref="${cellRef}"]`).addClass("drag-target")
        }
      })
    }
  }

  // Get drag range
  function getDragRange() {
    if (!dragStart || !dragEnd) return []

    const startParsed = formulaParser.parseCellReference(dragStart)
    const endParsed = formulaParser.parseCellReference(dragEnd)

    const minCol = Math.min(startParsed.colIndex, endParsed.colIndex)
    const maxCol = Math.max(startParsed.colIndex, endParsed.colIndex)
    const minRow = Math.min(startParsed.rowIndex, endParsed.rowIndex)
    const maxRow = Math.max(startParsed.rowIndex, endParsed.rowIndex)

    const range = []
    for (let row = minRow; row <= maxRow; row++) {
      for (let col = minCol; col <= maxCol; col++) {
        const cellRef = `${formulaParser.getColumnName(col)}${row + 1}`
        range.push(cellRef)
      }
    }
    return range
  }

  // Fill drag range
  function fillDragRange() {
    const sourceCell = formulaParser.parseCellReference(dragStart)
    const sourceValue = cells[dragStart]?.value || ""

    if (!sourceValue) return

    const range = getDragRange()

    range.forEach((cellRef) => {
      if (cellRef === dragStart) return // Skip source cell

      const targetCell = formulaParser.parseCellReference(cellRef)
      const rowDelta = targetCell.rowIndex - sourceCell.rowIndex
      const colDelta = targetCell.colIndex - sourceCell.colIndex

      let newValue = sourceValue

      if (sourceValue.startsWith("=")) {
        // It's a formula, increment the references
        newValue = formulaParser.incrementFormula(sourceValue, rowDelta, colDelta)
      } else if (!isNaN(Number(sourceValue))) {
        // It's a number, increment it
        const baseNumber = Number(sourceValue)
        const increment = Math.abs(rowDelta) > Math.abs(colDelta) ? rowDelta : colDelta
        newValue = String(baseNumber + increment)
      }
      // For text values, just copy as-is

      updateCell(cellRef, newValue)
    })
  }

  // Export/Import Functions

  // Get all headers (column names)
  function getHeaders() {
    const headers = []
    for (let i = 0; i < COLS; i++) {
      headers.push(formulaParser.getColumnName(i))
    }
    return headers
  }

  // Get all data from the spreadsheet
  function getAllData() {
    const data = []

    for (let row = 1; row <= ROWS; row++) {
      const rowData = {}
      let hasData = false

      for (let col = 0; col < COLS; col++) {
        const colName = formulaParser.getColumnName(col)
        const cellRef = `${colName}${row}`
        const cellValue = getCellDisplayValue(cellRef)

        rowData[colName] = cellValue
        if (cellValue !== "") {
          hasData = true
        }
      }

      // Only include rows that have at least one cell with data
      if (hasData) {
        data.push(rowData)
      }
    }

    return data
  }

  // Get data as array of arrays (including headers)
  function getDataAsArray(includeHeaders = true) {
    const result = []

    if (includeHeaders) {
      result.push(getHeaders())
    }

    for (let row = 1; row <= ROWS; row++) {
      const rowData = []
      let hasData = false

      for (let col = 0; col < COLS; col++) {
        const colName = formulaParser.getColumnName(col)
        const cellRef = `${colName}${row}`
        const cellValue = getCellDisplayValue(cellRef)

        rowData.push(cellValue)
        if (cellValue !== "") {
          hasData = true
        }
      }

      // Only include rows that have at least one cell with data
      if (hasData) {
        result.push(rowData)
      }
    }

    return result
  }

  // Get data with formulas (raw values)
  function getAllDataWithFormulas() {
    const data = []

    for (let row = 1; row <= ROWS; row++) {
      const rowData = {}
      let hasData = false

      for (let col = 0; col < COLS; col++) {
        const colName = formulaParser.getColumnName(col)
        const cellRef = `${colName}${row}`
        const cellValue = getCellValue(cellRef) // Raw value including formulas

        rowData[colName] = cellValue
        if (cellValue !== "") {
          hasData = true
        }
      }

      // Only include rows that have at least one cell with data
      if (hasData) {
        data.push(rowData)
      }
    }

    return data
  }

  // Get data for a specific range
  function getRangeData(startCell, endCell) {
    const startParsed = formulaParser.parseCellReference(startCell)
    const endParsed = formulaParser.parseCellReference(endCell)

    const minCol = Math.min(startParsed.colIndex, endParsed.colIndex)
    const maxCol = Math.max(startParsed.colIndex, endParsed.colIndex)
    const minRow = Math.min(startParsed.row, endParsed.row)
    const maxRow = Math.max(startParsed.row, endParsed.row)

    const data = []

    for (let row = minRow; row <= maxRow; row++) {
      const rowData = []
      for (let col = minCol; col <= maxCol; col++) {
        const colName = formulaParser.getColumnName(col)
        const cellRef = `${colName}${row}`
        const cellValue = getCellDisplayValue(cellRef)
        rowData.push(cellValue)
      }
      data.push(rowData)
    }

    return data
  }

  // Export to CSV format
  function exportToCSV() {
    const data = getDataAsArray(true)
    const csvContent = data
      .map((row) =>
        row
          .map((cell) => {
            // Escape quotes and wrap in quotes if contains comma, quote, or newline
            const cellStr = String(cell)
            if (cellStr.includes(",") || cellStr.includes('"') || cellStr.includes("\n")) {
              return '"' + cellStr.replace(/"/g, '""') + '"'
            }
            return cellStr
          })
          .join(","),
      )
      .join("\n")

    return csvContent
  }

  // Export to JSON format
  function exportToJSON() {
    return JSON.stringify(getAllData(), null, 2)
  }

  // Print data to console (for debugging)
  function printSpreadsheetData() {
    console.log("=== SPREADSHEET DATA ===")
    console.log("Headers:", getHeaders())
    console.log("Data (Objects):", getAllData())
    console.log("Data (Array):", getDataAsArray())
    console.log("Data with Formulas:", getAllDataWithFormulas())
    console.log("CSV Format:", exportToCSV())
    console.log("JSON Format:", exportToJSON())
  }

  // Download data as file
  function downloadData(format = "csv") {
    let content, filename, mimeType

    switch (format.toLowerCase()) {
      case "csv":
        content = exportToCSV()
        filename = "spreadsheet_data.csv"
        mimeType = "text/csv"
        break
      case "json":
        content = exportToJSON()
        filename = "spreadsheet_data.json"
        mimeType = "application/json"
        break
      default:
        console.error('Unsupported format. Use "csv" or "json"')
        return
    }

    const blob = new Blob([content], { type: mimeType })
    const url = URL.createObjectURL(blob)
    const a = document.createElement("a")
    a.href = url
    a.download = filename
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
  }

  // Make functions globally available
  window.spreadsheetAPI = {
    getHeaders,
    getAllData,
    getDataAsArray,
    getAllDataWithFormulas,
    getRangeData,
    exportToCSV,
    exportToJSON,
    printSpreadsheetData,
    downloadData,
  }

  // Initialize the spreadsheet
  initSpreadsheet()
})
