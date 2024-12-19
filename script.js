// 讓小機器人動起來
document.addEventListener("DOMContentLoaded", () => {
    const robots = [
        { id: "robot1", startX: -60, startY: 50, endX: 300 },
        { id: "robot2", startX: window.innerWidth + 60, startY: 80, endX: 200 },
        { id: "robot3", startX: -60, startY: 120, endX: 400 },
        { id: "robot4", startX: window.innerWidth + 60, startY: 150, endX: 500 },
    ];

    robots.forEach(robot => {
        const element = document.getElementById(robot.id);
        if (!element) {
            console.error(`Element with ID ${robot.id} not found!`);
            return; // 跳過未找到的元素
        }

        // 設置初始位置
        element.style.left = `${robot.startX}px`;
        element.style.top = `${robot.startY}px`;

        // 執行動畫
        setTimeout(() => {
            element.animate(
                [
                    { transform: `translateX(0px)` }, // 初始位置
                    { transform: `translateX(${robot.endX - robot.startX}px)` } // 終點位置
                ],
                {
                    duration: 2000 + Math.random() * 1000, // 動畫持續時間隨機
                    iterations: Infinity, // 無限循環
                    direction: 'alternate', // 動畫往返
                    easing: 'ease-in-out', // 平滑的運動效果
                }
            );
        }, Math.random() * 1000); // 為每個機器人增加隨機延遲
    });
});
