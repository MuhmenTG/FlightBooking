import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ShowFlightofferComponent } from './show-flightoffer.component';

describe('ShowFlightofferComponent', () => {
  let component: ShowFlightofferComponent;
  let fixture: ComponentFixture<ShowFlightofferComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ShowFlightofferComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ShowFlightofferComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
