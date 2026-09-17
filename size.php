                                   Selected Size:
<div class="day-picker">
   <button class="day disabled">6</button>
   <button class="day active">7</button>
   <button class="day disabled">8</button>
   <button class="day">9</button>
   <button class="day disabled">10</button>
</div>
<style>
   .day-picker {
   display: flex;
   gap: 8px;
   overflow-x: auto;
   padding: 8px 0;
   /* Hide scrollbar */
   scrollbar-width: none;
   }
   .day-picker::-webkit-scrollbar {
   display: none;
   }
   .day {
   min-width: 48px;
   width: 48px;
   height: 48px;
   border: 1px solid #E5E5E5;
   border-radius: 12px;
   background: #fff;
   /*color: #222;*/
   color: #BDBDBD;
   font-size: 15px;
   font-weight: 500;
   flex-shrink: 0;
   }
   .day.active {
   background: #1F1F1F;
   /*color: #fff;*/
   color: #BDBDBD;
   border-color: #1F1F1F;
   }
   /*.day.disabled {
   background: #F5F5F5;
   color: #BDBDBD;
   border-style: dashed;
   }*/
   @media (max-width: 768px) {
   .day {
   width: 44px;
   min-width: 44px;
   height: 44px;
   border-radius: 10px;
   font-size: 15px;
   }
   .day-picker {
   gap: 5px;
   padding: 0 16px;
   }
   }                             
</style>