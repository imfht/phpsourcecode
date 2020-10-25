/**
 * Created by spatra on 15-3-7.
 */

define(['libraryJS/datetime/ng-require', 'angular', 'angularMocks'], function(currentModule){
  beforeEach( module(currentModule) );

  describe('DatetimeCheckRangeService', function(){
    var DatetimeCheckRangeService;
    var oneDaySeconds = 1000 * 60 * 60 *24;

    function getDateString(datetime){
      return datetime.getFullYear() + '-'
        + (datetime.getMonth() + 1) + '-'
        + datetime.getDate();
    }

    beforeEach(inject(function(_DatetimeCheckRangeService_){
      DatetimeCheckRangeService = _DatetimeCheckRangeService_;
    }));

    it('method: setNow/getNow', function(){
      DatetimeCheckRangeService.setNow('1993-03-09');

      expect(DatetimeCheckRangeService.getNow()).toBeDefined();
      expect( DatetimeCheckRangeService.getNow() instanceof Date );
    });

    it('method: checkRange', function(){
      DatetimeCheckRangeService.setNow(); //重置

      var currentDatetime = new Date;

      //检查判断是否是当天
      expect( DatetimeCheckRangeService.checkRange(currentDatetime, 'today')).toBeTruthy();
      expect( DatetimeCheckRangeService.checkRange( getDateString(currentDatetime), 'today' ) ).toBeTruthy();
      expect( DatetimeCheckRangeService.checkRange( '1993-03-09', 'today')).toBeFalsy();

      //添加是否从当前时间点计算，后两天之内
      expect(DatetimeCheckRangeService.checkRange( currentDatetime.getTime() + oneDaySeconds, 'twodays') ).toBeTruthy();
      expect( DatetimeCheckRangeService.checkRange( currentDatetime.getTime() + oneDaySeconds * 3, 'twodays')).toBeFalsy();

      //检查两个日期是否处于同一周
      DatetimeCheckRangeService.setNow('2014-12-24');
      expect(DatetimeCheckRangeService.checkRange('2014-12-25', 'week') ).toBeTruthy();
      expect(DatetimeCheckRangeService.checkRange('2014-12-20', 'week')).toBeFalsy();

      //检查两个日期是否处于同一个年月
      DatetimeCheckRangeService.setNow('1993-03-09');
      expect( DatetimeCheckRangeService.checkRange('1993-03-10', 'month') ).toBeTruthy();
      expect( DatetimeCheckRangeService.checkRange('1993-04-09', 'month') ).toBeFalsy();
      expect( DatetimeCheckRangeService.checkRange('1992-03-09', 'month') ).toBeFalsy();
    });
  });//End of --> DatetimeCheckRangeService

});