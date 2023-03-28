import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ShowFlightoffersComponent } from './show-flightoffers.component';

describe('ShowFlightoffersComponent', () => {
  let component: ShowFlightoffersComponent;
  let fixture: ComponentFixture<ShowFlightoffersComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ShowFlightoffersComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ShowFlightoffersComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
