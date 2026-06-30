#!/bin/bash
# TestSprite Loop - Portfolio Febryanus
# Runs every 15 minutes

PROJECT_ID="15d12f26-33f5-47ae-b6a3-ad330e7e36bd"
LOG_FILE="/root/mycvebry/portfolio-febryanus/storage/logs/testsprite-loop.log"

echo "=== TestSprite Loop Run: $(date) ===" >> $LOG_FILE

# Run test
RESULT=$(testsprite test run --project $PROJECT_ID --wait --timeout 300 2>&1)
echo "$RESULT" >> $LOG_FILE

# Check if passed
if echo "$RESULT" | grep -q "passed"; then
    echo "✅ Test PASSED - $(date)" >> $LOG_FILE
else
    echo "❌ Test FAILED - $(date)" >> $LOG_FILE
    echo "ALERT: TestSprite test failed!" | tee /dev/stderr
fi

echo "=== End Run ===" >> $LOG_FILE
