// Formula Parser and Calculator
class FormulaParser {
    constructor() {
      this.functions = {
        SUM: (values) => values.reduce((sum, val) => sum + val, 0),
        AVERAGE: (values) => (values.length ? values.reduce((sum, val) => sum + val, 0) / values.length : 0),
        MIN: (values) => Math.min(...values),
        MAX: (values) => Math.max(...values),
        COUNT: (values) => values.filter((val) => !isNaN(val)).length,
        ROUND: (value, digits = 0) => Math.round(value * Math.pow(10, digits)) / Math.pow(10, digits),
      }
    }
  
    parseFormula(formula, getCellValue) {
      if (!formula.startsWith("=")) {
        return formula
      }
  
      let expression = formula.slice(1) // Remove the '=' sign
  
      // Handle range references (A1:A5) first so that individual cell replacement doesn't break them
      expression = expression.replace(/([A-Z]+\d+):([A-Z]+\d+)/g, (match, start, end) => {
        const values = this.getRangeValues(start, end, getCellValue)
        return `[${values.join(",")}]`
      })

      // Replace remaining individual cell references (A1, B2, etc.) with their computed values
      expression = expression.replace(/\b[A-Z]+\d+\b/g, (match) => {
        let value = getCellValue(match)
        // If the referenced cell contains a formula, evaluate it
        if (typeof value === "string" && value.startsWith("=")) {
          value = this.parseFormula(value, getCellValue)
        }
        return isNaN(Number(value)) ? "0" : String(value)
      })
  
      // Handle functions
      Object.keys(this.functions).forEach((funcName) => {
        // Match function calls like SUM(...)
        const regex = new RegExp(`${funcName}\\(([^)]*)\\)`, "gi")
        expression = expression.replace(regex, (match, args) => {
          try {
            let argValues = []
  
            if (args.includes("[") && args.includes("]")) {
              // Handle array arguments (from ranges)
              const arrayMatch = args.match(/\[([^\]]+)\]/)
              if (arrayMatch) {
                argValues = arrayMatch[1]
                  .split(",")
                  .map((v) => Number.parseFloat(v.trim()))
                  .filter((v) => !isNaN(v))
              }
            } else {
              // Disallow individual arguments like A1,A2; only range syntax (e.g., A1:A2) is supported
              throw new Error("Invalid argument format: use ':' for ranges, not commas")
            }
  
            const func = this.functions[funcName]
            if (typeof func === "function") {
              return String(func(argValues))
            }
          } catch (e) {
            return "0"
          }
          return "0"
        })
      })
  
      try {
        // Evaluate the mathematical expression
        return Function(`"use strict"; return (${expression})`)()
      } catch (e) {
        return "#ERROR"
      }
    }
  
    getRangeValues(start, end, getCellValue) {
      const startCol = start.match(/[A-Z]+/)[0]
      const startRow = Number.parseInt(start.match(/\d+/)[0])
      const endCol = end.match(/[A-Z]+/)[0]
      const endRow = Number.parseInt(end.match(/\d+/)[0])
  
      const values = []
  
      for (let row = startRow; row <= endRow; row++) {
        for (let col = startCol; col <= endCol; col = this.getNextColumn(col)) {
          const cellRef = `${col}${row}`
          let value = getCellValue(cellRef)
          if (typeof value === "string" && value.startsWith("=")) {
            value = this.parseFormula(value, getCellValue)
          }
          const numValue = Number.parseFloat(value)
          if (!isNaN(numValue)) {
            values.push(numValue)
          }
          if (col === endCol) break
        }
      }
  
      return values
    }
  
    getNextColumn(col) {
      let result = ""
      let carry = 1
  
      for (let i = col.length - 1; i >= 0; i--) {
        let charCode = col.charCodeAt(i) - 65 + carry
        if (charCode > 25) {
          carry = 1
          charCode = 0
        } else {
          carry = 0
        }
        result = String.fromCharCode(charCode + 65) + result
      }
  
      if (carry) {
        result = "A" + result
      }
  
      return result
    }
  
    getColumnName(index) {
      let result = ""
      while (index >= 0) {
        result = String.fromCharCode(65 + (index % 26)) + result
        index = Math.floor(index / 26) - 1
      }
      return result
    }
  
    incrementFormula(formula, rowDelta, colDelta) {
      if (!formula.startsWith("=")) {
        return formula
      }
  
      return formula.replace(/([A-Z]+)(\d+)/g, (match, col, row) => {
        const newRow = Math.max(1, Number.parseInt(row) + rowDelta)
        const newCol = this.incrementColumn(col, colDelta)
        return `${newCol}${newRow}`
      })
    }
  
    incrementColumn(col, delta) {
      if (delta === 0) return col
  
      let colIndex = 0
      for (let i = 0; i < col.length; i++) {
        colIndex = colIndex * 26 + (col.charCodeAt(i) - 65 + 1)
      }
  
      colIndex = Math.max(1, colIndex + delta)
  
      let result = ""
      while (colIndex > 0) {
        colIndex--
        result = String.fromCharCode(65 + (colIndex % 26)) + result
        colIndex = Math.floor(colIndex / 26)
      }
  
      return result || "A"
    }
  
    adjustFormulaReferences(formula, fromCell, toCell) {
      if (!formula.startsWith("=")) {
        return formula
      }
  
      const fromParsed = this.parseCellReference(fromCell)
      const toParsed = this.parseCellReference(toCell)
  
      const rowDelta = toParsed.row - fromParsed.row
      const colDelta = toParsed.colIndex - fromParsed.colIndex
  
      return this.incrementFormula(formula, rowDelta, colDelta)
    }
  
    parseCellReference(cellRef) {
      const col = cellRef.match(/[A-Z]+/)[0]
      const row = Number.parseInt(cellRef.match(/\d+/)[0])
      const colIndex = col.split("").reduce((acc, char) => acc * 26 + (char.charCodeAt(0) - 64), 0) - 1
      return { col, row, colIndex, rowIndex: row - 1 }
    }
  }
  
  // Global instance
  window.formulaParser = new FormulaParser()
  